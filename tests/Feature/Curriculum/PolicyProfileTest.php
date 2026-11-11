<?php

declare(strict_types=1);

use App\Curriculum\Import\CurriculumImporter;
use App\Curriculum\Policy\LevelProfiles;
use App\Curriculum\Policy\PolicyProfileResolver;
use App\Curriculum\Policy\RuleSetDates;
use App\Curriculum\Policy\SchoolYear;
use App\Enums\CurriculumVersionStatus;
use App\Enums\SchoolLevel;
use App\Models\CurriculumVersion;
use App\Models\Region;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Symfony\Component\Finder\Finder;

beforeEach(function (): void {
    foreach ([
        'docs/data/curriculum/subjects.json',
        'docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json',
        'docs/data/curriculum/mi-allocation-kma-1503-2025.json',
    ] as $file) {
        app(CurriculumImporter::class)->import(base_path($file));
    }
});

/**
 * A temporary profile folder, so a test can list its own rule sets.
 *
 * @param  array<string, list<array<string, mixed>>>  $ruleSets
 */
function profileFolder(array $ruleSets): void
{
    $dir = storage_path('framework/testing/profiles-'.uniqid());
    @mkdir($dir, 0777, true);

    foreach (['sd', 'mi'] as $level) {
        $data = json_decode((string) file_get_contents(base_path("docs/data/profiles/{$level}.json")), true);
        $data['versions'][0]['rule_sets'] = $ruleSets;
        file_put_contents("{$dir}/{$level}.json", json_encode($data));
    }

    config(['curriculum.profiles' => str_replace(base_path().'/', '', $dir)]);
    app()->forgetInstance(LevelProfiles::class);
    app(PolicyProfileResolver::class)->flush();
}

it('copies the version dates from the Profil Jenjang on import (D-355)', function (): void {
    $sd = CurriculumVersion::query()->where('code', 'STRUKTUR-SD-PERMENDIKDASMEN-13-2025')->sole();
    $mi = CurriculumVersion::query()->where('code', 'ALOKASI-MI-KMA-1503-2025')->sole();

    expect($sd->effective_on?->toDateString())->toBe('2025-07-15')
        ->and($sd->applies_from_school_year)->toBe('2025/2026')
        ->and($sd->supersedes_id)->toBeNull()
        ->and($mi->effective_on)->toBeNull()
        ->and($mi->applies_from_school_year)->toBe('2026/2027');
});

it('resolves the structure rule set per school year and level, replacing the config pointer (D-355)', function (): void {
    $resolver = app(PolicyProfileResolver::class);

    expect(config('curriculum.allocation_versions'))->toBeNull()
        ->and($resolver->resolve('2026/2027', SchoolLevel::Sd)->ruleSet('structure_jp'))->toBe('STRUKTUR-SD-PERMENDIKDASMEN-13-2025')
        ->and($resolver->resolve('2026/2027', SchoolLevel::Mi)->ruleSet('structure_jp'))->toBe('ALOKASI-MI-KMA-1503-2025')
        // Nothing older is loaded, so an earlier year uses the earliest rule set.
        ->and($resolver->resolve('2025/2026', SchoolLevel::Mi)->ruleSet('structure_jp'))->toBe('ALOKASI-MI-KMA-1503-2025')
        // Not imported in this test: no code rather than a guess.
        ->and($resolver->resolve('2026/2027', SchoolLevel::Sd)->ruleSet('tp_atp_defaults'))->toBeNull();
});

it('follows a newer version from its school year, skips retired ones, and prefers the province (D-354)', function (): void {
    CurriculumVersion::query()->create(['code' => 'STRUKTUR-SD-BARU-2027', 'name' => 'Baru', 'status' => CurriculumVersionStatus::Draft, 'meta' => [], 'imported_at' => now(), 'applies_from_school_year' => '2027/2028']);
    CurriculumVersion::query()->create(['code' => 'STRUKTUR-SD-JATENG', 'name' => 'Jateng', 'status' => CurriculumVersionStatus::Draft, 'meta' => [], 'imported_at' => now(), 'applies_from_school_year' => '2026/2027']);
    profileFolder(['structure_jp' => [
        ['code' => 'STRUKTUR-SD-PERMENDIKDASMEN-13-2025', 'applies_from_school_year' => '2025/2026'],
        ['code' => 'STRUKTUR-SD-BARU-2027', 'applies_from_school_year' => '2027/2028', 'supersedes' => 'STRUKTUR-SD-PERMENDIKDASMEN-13-2025'],
        ['code' => 'STRUKTUR-SD-JATENG', 'applies_from_school_year' => '2026/2027', 'region_province' => 'Jawa Tengah'],
    ]]);
    $resolver = app(PolicyProfileResolver::class);
    $kudus = Region::query()->where('regency', 'Kabupaten Kudus')->sole();
    $papua = Region::query()->create(['province' => 'Papua', 'regency' => 'Kabupaten Jayapura']);

    expect($resolver->resolve('2026/2027', SchoolLevel::Sd, $papua)->ruleSet('structure_jp'))->toBe('STRUKTUR-SD-PERMENDIKDASMEN-13-2025')
        ->and($resolver->resolve('2027/2028', SchoolLevel::Sd, $papua)->ruleSet('structure_jp'))->toBe('STRUKTUR-SD-BARU-2027')
        ->and($resolver->resolve('2026/2027', SchoolLevel::Sd, $kudus)->ruleSet('structure_jp'))->toBe('STRUKTUR-SD-JATENG');

    CurriculumVersion::query()->where('code', 'STRUKTUR-SD-BARU-2027')->update(['status' => CurriculumVersionStatus::Retired->value]);
    $resolver->flush();

    expect($resolver->resolve('2027/2028', SchoolLevel::Sd, $papua)->ruleSet('structure_jp'))->toBe('STRUKTUR-SD-PERMENDIKDASMEN-13-2025');

    // The import writes the predecessor too.
    app(CurriculumImporter::class)->import(base_path('docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json'));
    $version = CurriculumVersion::query()->where('code', 'STRUKTUR-SD-BARU-2027')->sole();
    app(RuleSetDates::class)->apply($version);
    $version->save();
    expect($version->fresh()->supersedes?->code)->toBe('STRUKTUR-SD-PERMENDIKDASMEN-13-2025');
});

it('caches per school year until the next import', function (): void {
    $resolver = app(PolicyProfileResolver::class);
    expect($resolver->resolve('2026/2027', SchoolLevel::Sd)->ruleSet('structure_jp'))->toBe('STRUKTUR-SD-PERMENDIKDASMEN-13-2025');

    CurriculumVersion::query()->where('code', 'STRUKTUR-SD-PERMENDIKDASMEN-13-2025')->update(['status' => CurriculumVersionStatus::Retired->value]);
    expect(app(PolicyProfileResolver::class)->resolve('2026/2027', SchoolLevel::Sd)->ruleSet('structure_jp'))->toBe('STRUKTUR-SD-PERMENDIKDASMEN-13-2025');

    app(CurriculumImporter::class)->import(base_path('docs/data/curriculum/mi-allocation-kma-1503-2025.json'));
    expect(app(PolicyProfileResolver::class)->resolve('2026/2027', SchoolLevel::Sd)->ruleSet('structure_jp'))->toBeNull();
});

it('keeps the level differences in the Profil Jenjang data (D-356, D-368)', function (): void {
    $sd = app(PolicyProfileResolver::class)->resolve('2026/2027', SchoolLevel::Sd)->level;
    $mi = app(PolicyProfileResolver::class)->resolve('2026/2027', SchoolLevel::Mi)->level;

    expect($sd->ministry()['code'])->toBe('kemendikdasmen')
        ->and($mi->ministry()['code'])->toBe('kemenag')
        ->and($sd->hasCharacterFramework('kbc'))->toBeFalse()
        ->and($mi->hasCharacterFramework('kbc'))->toBeTrue()
        ->and($sd->rpmExtraFields()['kbc']['enabled'])->toBeFalse()
        ->and($sd->rpmExtraFields()['kbc']['switchable_by_admin'])->toBeTrue()
        ->and($mi->rpmExtraFields()['kbc']['enabled'])->toBeTrue()
        ->and([$sd->reportExportTarget(), $mi->reportExportTarget()])->toBe(['e_rapor_sd', 'rdm'])
        ->and([$sd->homeroomJtmDefault(), $mi->homeroomJtmDefault()])->toBe([2, 6])
        ->and(array_column($sd->externalAssessments(), 'code'))->toBe(['an', 'tka'])
        ->and([$sd->dataOfRecord(), $mi->dataOfRecord()])->toBe(['dapodik', 'emis']);
});

it('has no code that branches on the school level (D-356)', function (): void {
    $hits = [];

    foreach (Finder::create()->files()->in(app_path())->name('*.php')->notPath('Enums') as $file) {
        if (preg_match('/SchoolLevel::(Sd|Mi)\b|[=!]==?\s*[\'"](sd|mi)[\'"]/', $file->getContents()) === 1) {
            $hits[] = $file->getRelativePathname();
        }
    }

    expect($hits)->toBe([]);
});

it('names school years and refuses malformed ones', function (): void {
    expect(SchoolYear::forDate(CarbonImmutable::parse('2026-07-01')))->toBe('2026/2027')
        ->and(SchoolYear::forDate(CarbonImmutable::parse('2027-06-30')))->toBe('2026/2027')
        ->and(SchoolYear::hasStarted('2027/2028', '2026/2027'))->toBeFalse()
        ->and(SchoolYear::hasStarted(null, '2026/2027'))->toBeTrue()
        ->and(fn () => SchoolYear::startYear('2026/2028'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => CurriculumVersion::query()->whereKey(CurriculumVersion::query()->value('id'))->update(['applies_from_school_year' => '2026-2027']))->toThrow(QueryException::class);
});
