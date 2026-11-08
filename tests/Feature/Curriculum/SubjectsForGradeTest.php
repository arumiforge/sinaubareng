<?php

declare(strict_types=1);

use App\Curriculum\Import\CurriculumImporter;
use App\Curriculum\SubjectOffer;
use App\Curriculum\SubjectsForGrade;
use App\Enums\SchoolLevel;
use App\Models\Region;
use App\Models\School;
use App\Models\SchoolSubject;
use App\Models\Subject;
use App\Tenancy\SchoolContext;

beforeEach(function (): void {
    foreach ([
        'docs/data/curriculum/subjects.json',
        'docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json',
        'docs/data/curriculum/mi-allocation-kma-1503-2025.json',
    ] as $file) {
        app(CurriculumImporter::class)->import(base_path($file));
    }

    $this->kudus = Region::query()->where('regency', 'Kabupaten Kudus')->sole();
});

/**
 * Subject codes a grade takes at the school, as the school itself would see them.
 *
 * @return array<string, bool> code => in report
 */
function subjectsFor(School $school, int $grade): array
{
    return app(SchoolContext::class)->runFor($school->id, fn (): array => app(SubjectsForGrade::class)
        ->forSchool($school, $grade)
        ->mapWithKeys(fn (SubjectOffer $offer): array => [$offer->subject->code => $offer->inReport])
        ->all());
}

function subjectId(string $code): int
{
    return Subject::query()->where('code', $code)->value('id');
}

function switchSubject(School $school, string $code, ?int $grade, bool $enabled, bool $inReport = false): void
{
    app(SchoolContext::class)->runFor($school->id, fn () => SchoolSubject::query()->create([
        'subject_id' => subjectId($code), 'grade' => $grade, 'enabled' => $enabled, 'in_report' => $inReport,
    ]));
}

it('gives an SD class the national subjects and the region\'s local content', function (): void {
    $kudus = School::factory()->create(['level' => SchoolLevel::Sd, 'region_id' => $this->kudus->id]);
    $elsewhere = School::factory()->create(['level' => SchoolLevel::Sd]);
    $national = ['PAI', 'PAKR', 'PAKT', 'PAH', 'PAB', 'PAKH', 'PP', 'BIND', 'MTK', 'PJOK', 'SB'];

    expect(array_keys(subjectsFor($kudus, 1)))->toBe([...$national, 'BJAW'])
        ->and(array_keys(subjectsFor($elsewhere, 1)))->toBe($national)
        ->and(array_keys(subjectsFor($elsewhere, 5)))->toBe([...array_slice($national, 0, 9), 'IPAS', 'PJOK', 'SB', 'BING'])
        ->and(subjectsFor($kudus, 1))->each->toBeTrue();
});

it('gives an MI class the Kemenag subjects instead of PAI', function (): void {
    $school = School::factory()->create(['level' => SchoolLevel::Mi]);

    expect(array_keys(subjectsFor($school, 3)))->toBe(['QH', 'AA', 'FIK', 'SKI', 'BAR', 'PP', 'BIND', 'MTK', 'IPAS', 'PJOK', 'SB', 'BING'])
        ->and(array_keys(subjectsFor($school, 1)))->not->toContain('SKI', 'IPAS', 'BING', 'KKA')
        ->and(array_keys(subjectsFor($school, 6)))->not->toContain('KKA');
});

it('keeps optional content off until the school switches it on, outside the rapor by default', function (): void {
    $school = School::factory()->create(['level' => SchoolLevel::Sd]);
    switchSubject($school, 'BING', 1, true);
    switchSubject($school, 'IPAS', null, true, inReport: true);
    switchSubject($school, 'TB', null, true, inReport: true);

    expect(subjectsFor($school, 1))->toMatchArray(['BING' => false, 'IPAS' => true, 'TB' => true])
        ->and(subjectsFor($school, 2))->not->toHaveKey('BING')
        ->and(subjectsFor($school, 2))->toHaveKey('IPAS');
});

it('lets a school switch local content off, and back on for one grade', function (): void {
    $school = School::factory()->create(['level' => SchoolLevel::Sd, 'region_id' => $this->kudus->id]);
    switchSubject($school, 'BJAW', null, false);
    switchSubject($school, 'BJAW', 3, true, inReport: true);

    expect(subjectsFor($school, 1))->not->toHaveKey('BJAW')
        ->and(subjectsFor($school, 3))->toHaveKey('BJAW');
});

it('does not let a school switch off a compulsory subject or take another level\'s subject', function (): void {
    $school = School::factory()->create(['level' => SchoolLevel::Sd]);
    switchSubject($school, 'MTK', null, false);
    switchSubject($school, 'QH', null, true);
    switchSubject($school, 'KKA', 5, true);

    expect(subjectsFor($school, 5))->toHaveKey('MTK')
        ->and(subjectsFor($school, 5))->not->toHaveKeys(['QH', 'KKA']);
});

it('never uses another school\'s choices', function (): void {
    $schoolA = School::factory()->create(['level' => SchoolLevel::Sd, 'region_id' => $this->kudus->id]);
    $schoolB = School::factory()->create(['level' => SchoolLevel::Sd, 'region_id' => $this->kudus->id]);
    switchSubject($schoolB, 'BING', 1, true);
    switchSubject($schoolB, 'BJAW', null, false);

    expect(subjectsFor($schoolA, 1))->toHaveKey('BJAW')
        ->and(subjectsFor($schoolA, 1))->not->toHaveKey('BING');
});

it('refuses to resolve subjects for a school other than the current one', function (): void {
    $schoolA = School::factory()->create();
    $schoolB = School::factory()->create();

    app(SchoolContext::class)->set($schoolA->id);

    expect(fn () => app(SubjectsForGrade::class)->forSchool($schoolB, 1))->toThrow(LogicException::class);
});

it('scopes school subject rows to the current school (IDOR)', function (): void {
    $schoolA = School::factory()->create();
    $schoolB = School::factory()->create();
    SchoolSubject::factory()->for($schoolA)->create();
    $foreign = SchoolSubject::factory()->for($schoolB)->create();

    expect(SchoolSubject::query()->count())->toBe(0);

    app(SchoolContext::class)->set($schoolA->id);

    expect(SchoolSubject::query()->pluck('school_id')->all())->toBe([$schoolA->id])
        ->and(SchoolSubject::query()->find($foreign->id))->toBeNull()
        ->and(fn () => SchoolSubject::factory()->for($schoolB)->create())->toThrow(LogicException::class);
});
