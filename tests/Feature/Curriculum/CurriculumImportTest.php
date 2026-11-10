<?php

declare(strict_types=1);

use App\Curriculum\Import\CurriculumImporter;
use App\Curriculum\Import\ImportReport;
use App\Curriculum\Import\InvalidCurriculumFile;
use App\Curriculum\Import\SourceOverrides;
use App\Curriculum\Import\TopicInput;
use App\Enums\CurriculumVersionStatus;
use App\Enums\LearningType;
use App\Enums\Phase;
use App\Models\CurriculumVersion;
use App\Models\FocusItem;
use App\Models\LearningObjective;
use App\Models\LearningOutcome;
use App\Models\Region;
use App\Models\RegionSubject;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use App\Models\Topic;

function curriculumFixture(string $name): string
{
    return base_path("tests/Fixtures/curriculum/{$name}");
}

function importCatalogue(): void
{
    app(CurriculumImporter::class)->import(base_path('docs/data/curriculum/subjects.json'));
}

/**
 * Writes JSON to a temporary file and returns its path.
 *
 * @param  array<array-key, mixed>  $json
 */
function curriculumTempFile(array $json): string
{
    $path = tempnam(sys_get_temp_dir(), 'curriculum').'.json';
    file_put_contents($path, json_encode($json, JSON_UNESCAPED_UNICODE));

    return $path;
}

/**
 * @return array<array-key, mixed>
 */
function curriculumJson(string $path): array
{
    return json_decode((string) file_get_contents($path), true);
}

it('imports every curriculum file in the repository as draft versions', function (): void {
    $this->artisan('curriculum:import')
        ->doesntExpectOutputToContain('data mismatch(es) to review.')
        ->assertSuccessful();

    expect(CurriculumVersion::query()->orderBy('code')->pluck('status', 'code')->map->value->all())->toBe([
        'ALOKASI-MI-KMA-1503-2025' => 'draft',
        'CP-PABP-BKPDM-020-2026' => 'draft',
        'CP-PAI-BA-MADRASAH-9941-2025' => 'draft',
        'SIGRAK-MI-2026-R1' => 'draft',
        'SIGRAK-SD-2026-R1' => 'draft',
        'STRUKTUR-SD-PERMENDIKDASMEN-13-2025' => 'draft',
    ])
        ->and(Subject::query()->count())->toBe(22)
        ->and(LearningOutcome::query()->count())->toBe(199)
        ->and(Topic::query()->count())->toBe(956)
        ->and(LearningObjective::query()->count())->toBe(2695)
        ->and(FocusItem::query()->count())->toBe(4069)
        ->and(RegionSubject::query()->sole()->region->regency)->toBe('Kabupaten Kudus');
});

it('fills SD allocations from Permendikdasmen 13/2025, with kokurikuler and without Koding', function (): void {
    $this->artisan('curriculum:import')->assertSuccessful();
    $version = CurriculumVersion::query()->where('code', 'STRUKTUR-SD-PERMENDIKDASMEN-13-2025')->sole();
    $row = fn (string $subject, int $grade, LearningType $type): ?SubjectAllocation => SubjectAllocation::query()
        ->whereBelongsTo($version)->where('grade', $grade)->where('learning_type', $type->value)
        ->whereRelation('subject', 'code', $subject)->first();

    // One religion row in the table fills all six religion subjects.
    expect(SubjectAllocation::query()->whereBelongsTo($version)->where('grade', 6)->where('learning_type', 'intracurricular')
        ->whereRelation('subject', 'religion', '!=', null)->count())->toBe(6)
        ->and($row('PAKH', 6, LearningType::Intracurricular)?->only(['jp_per_week', 'weeks_per_year', 's1_jp', 's1_effective_weeks', 's2_jp', 's2_effective_weeks']))
        ->toBe(['jp_per_week' => 3, 'weeks_per_year' => 32, 's1_jp' => 54, 's1_effective_weeks' => 18, 's2_jp' => 42, 's2_effective_weeks' => 14])
        ->and($row('MTK', 1, LearningType::Cocurricular)?->s1_jp)->toBe(18)
        ->and($row('BIG', 3, LearningType::Intracurricular)?->jp_per_week)->toBe(2)
        ->and($row('BIG', 3, LearningType::Cocurricular))->toBeNull()
        ->and($row('BIG', 1, LearningType::Intracurricular))->toBeNull()
        ->and($row('IPAS', 2, LearningType::Intracurricular))->toBeNull()
        ->and($row('KKA', 5, LearningType::Intracurricular))->toBeNull()
        ->and($version->fileMeta('local_content_max_jp_per_year'))->toBe(['1' => 72, '2' => 72, '3' => 72, '4' => 72, '5' => 72, '6' => 64]);
});

it('changes nothing when the same files are imported again', function (): void {
    $this->artisan('curriculum:import')->assertSuccessful();
    $importedAt = CurriculumVersion::query()->pluck('imported_at', 'code');

    $this->travel(1)->day();
    $changed = collect(config('curriculum.files'))
        ->filter(fn (array $file): bool => is_file(base_path($file['path'])))
        ->sum(fn (array $file): int => app(CurriculumImporter::class)->import(base_path($file['path']))->changedRows());

    expect($changed)->toBe(0)
        ->and(CurriculumVersion::query()->pluck('imported_at', 'code')->map->toIso8601String()->all())
        ->toBe($importedAt->map->toIso8601String()->all());
});

it('keeps the status a version was given', function (): void {
    importCatalogue();
    $file = base_path('docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json');
    app(CurriculumImporter::class)->import($file);
    CurriculumVersion::query()->update(['status' => CurriculumVersionStatus::Active]);

    app(CurriculumImporter::class)->import($file);

    expect(CurriculumVersion::query()->sole()->status)->toBe(CurriculumVersionStatus::Active);
});

it('keeps CP rows with their phase summary, Arabic passages, and review notes', function (): void {
    importCatalogue();
    app(CurriculumImporter::class)->import(base_path('docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json'));
    app(CurriculumImporter::class)->import(base_path('docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json'));

    $kristenA = LearningOutcome::query()->whereRelation('subject', 'code', 'PAKR')->where('phase', 'A')->orderBy('order')->get();
    $arabic = LearningOutcome::query()->whereRelation('subject', 'code', 'BAR')->where('phase', 'A')->where('element', 'Komponen Bahasa')->sole();

    // Ten sub-elements, two without CP in Fase A, plus the summary row.
    expect($kristenA)->toHaveCount(9)
        ->and($kristenA->first()->element)->toBeNull()
        ->and($kristenA->first()->code)->toBe('CP-PABP-BKPDM-020-2026-PAKR-A-00')
        ->and($kristenA->pluck('sub_element')->filter()->contains('Allah Penyelamat'))->toBeFalse()
        ->and($arabic->extras['arabic_passages'])->toHaveCount(2)
        ->and($arabic->extras['review_notes'])->not->toBeEmpty()
        ->and($arabic->extras['lead_in'])->toBe('Pada akhir fase A, murid memiliki kemampuan sebagai berikut.');
});

it('reports data mismatches and still imports the rows', function (): void {
    importCatalogue();
    $report = app(CurriculumImporter::class)->import(curriculumFixture('topics-with-mismatches.json'));

    expect($report->mismatches())->toBe([
        'Topic SIGRAK-TEST-2026-R1-PAKR-K1-02: grade 1 is in Fase A, the file says Fase B.',
        'Topic SIGRAK-TEST-2026-R1-PAKR-K1-02: fokus JP add up to 6, jp_total is 9.',
        'Topic SIGRAK-TEST-2026-R1-PAKR-K1-02: weekly distribution adds up to 6, jp_total is 9.',
        'Fokus SIGRAK-TEST-2026-R1-PAKR-K1-02-FM01 names TP SIGRAK-TEST-2026-R1-PAKR-K1-01-TP01, which is not a TP of its topic; link skipped.',
        'PAKR grade 1 semester 1: topics add up to 15 JP, the allocation is 12 JP.',
    ])
        ->and(Topic::query()->count())->toBe(2)
        ->and(FocusItem::query()->where('code', 'SIGRAK-TEST-2026-R1-PAKR-K1-01-FM02')->sole()->learningObjectives->pluck('code')->all())->toBe(['SIGRAK-TEST-2026-R1-PAKR-K1-01-TP02'])
        ->and(FocusItem::query()->where('code', 'SIGRAK-TEST-2026-R1-PAKR-K1-02-FM01')->sole()->learningObjectives)->toBeEmpty()
        ->and(Topic::query()->where('code', 'SIGRAK-TEST-2026-R1-PAKR-K1-01')->sole()->extras)->toBe(['sub_element' => 'Allah Pencipta']);
});

it('fails on mismatches only with --strict', function (): void {
    importCatalogue();

    $this->artisan('curriculum:import', ['files' => [curriculumFixture('topics-with-mismatches.json')]])
        ->expectsOutputToContain('Mismatch PAKR grade 1 semester 1: topics add up to 15 JP, the allocation is 12 JP.')
        ->expectsOutputToContain('5 data mismatch(es) to review.')
        ->assertSuccessful();

    $this->artisan('curriculum:import', ['files' => [curriculumFixture('topics-with-mismatches.json')], '--strict' => true])
        ->assertFailed();
});

it('updates changed rows and keeps and reports rows a newer file no longer has', function (): void {
    importCatalogue();
    app(CurriculumImporter::class)->import(curriculumFixture('topics-with-mismatches.json'));

    $json = curriculumJson(curriculumFixture('topics-with-mismatches.json'));
    $json['subjects'][0]['topics'] = [$json['subjects'][0]['topics'][0]];
    $json['subjects'][0]['topics'][0]['title'] = 'Judul baru';
    $report = app(CurriculumImporter::class)->import(curriculumTempFile($json));

    expect(Topic::query()->where('code', 'SIGRAK-TEST-2026-R1-PAKR-K1-01')->value('title'))->toBe('Judul baru')
        ->and(Topic::query()->count())->toBe(2)
        ->and($report->counts()['topics'])->toBe(['created' => 0, 'updated' => 1, 'unchanged' => 0])
        ->and($report->mismatches())->toContain('Topic SIGRAK-TEST-2026-R1-PAKR-K1-02 is in the database but not in the file; it is kept.');
});

it('refuses an invalid file and writes nothing from it', function (Closure $file, string $message): void {
    importCatalogue();
    $subjects = Subject::query()->count();

    expect(fn () => app(CurriculumImporter::class)->import($file()))->toThrow(InvalidCurriculumFile::class, $message)
        ->and(CurriculumVersion::query()->count())->toBe(0)
        ->and(Topic::query()->count())->toBe(0)
        ->and(Subject::query()->count())->toBe($subjects);
})->with([
    'missing file' => [fn (): string => '/nonexistent/curriculum.json', 'File not found'],
    'not JSON' => [fn (): string => tap(tempnam(sys_get_temp_dir(), 'c'), fn (string $p) => file_put_contents($p, '{oops')), 'is not valid JSON'],
    'unknown format' => [fn (): string => curriculumTempFile(['hello' => 'world']), 'not a curriculum file this import knows'],
    'unknown subject' => [function (): string {
        $json = curriculumJson(curriculumFixture('topics-with-mismatches.json'));
        $json['subjects'][0]['code'] = 'XYZ';

        return curriculumTempFile($json);
    }, "unknown subject 'XYZ'"],
    'duplicate code' => [function (): string {
        $json = curriculumJson(curriculumFixture('topics-with-mismatches.json'));
        $json['subjects'][0]['topics'][1]['learning_objectives'][0]['code'] = 'SIGRAK-TEST-2026-R1-PAKR-K1-01-TP01';

        return curriculumTempFile($json);
    }, 'Code SIGRAK-TEST-2026-R1-PAKR-K1-01-TP01 appears more than once'],
    'bad semester' => [function (): string {
        $json = curriculumJson(curriculumFixture('topics-with-mismatches.json'));
        $json['subjects'][0]['topics'][1]['semester'] = 3;

        return curriculumTempFile($json);
    }, 'subjects[0].topics[1].semester must be 1 or 2'],
]);

it('rolls back a catalogue that fails half way', function (): void {
    $json = curriculumJson(base_path('docs/data/curriculum/subjects.json'));
    $json['subjects'][] = [...$json['subjects'][0], 'code' => 'NEW', 'name' => 'Baru', 'aliases' => [], 'kind' => 'elective'];

    expect(fn () => app(CurriculumImporter::class)->import(curriculumTempFile($json)))->toThrow(InvalidCurriculumFile::class, "'elective' must be one of")
        ->and(Subject::query()->count())->toBe(0)
        ->and(Region::query()->count())->toBe(0);
});

it('reads a SIGRAK file with curation, curation notes, and extras', function (): void {
    importCatalogue();
    $report = app(CurriculumImporter::class)->import(curriculumFixture('sigrak-topics.json'));
    $version = CurriculumVersion::query()->where('code', 'SIGRAK-TEST-2026-R1')->sole();
    $topic = Topic::query()->where('code', 'SIGRAK-TEST-2026-R1-MTK-K4-01')->sole();
    $meta = $version->meta['files']['tests/Fixtures/curriculum/sigrak-topics.json'];

    expect($version->status)->toBe(CurriculumVersionStatus::Draft)
        ->and($meta['dataset'])->toBe('sigrak_curriculum_topics')
        ->and(collect($meta['curation'])->map(fn (array $curation): string => $curation['status'])->sortKeys()->all())->toBe(['BIG' => 'converted', 'KKA' => 'converted', 'MTK' => 'curated'])
        ->and($meta['curation']['MTK']['reviewed_by'])->toBe('Tim Validasi SIGRAK')
        ->and($topic->semester)->toBe(1)
        ->and($topic->elements)->toBe('Bilangan; Aljabar')
        ->and($topic->references)->toMatchArray(['cp_source' => 'CP 2025', 'book_pages' => '1-10'])
        ->and($topic->curation_notes)->toMatchArray(['source_status' => 'disesuaikan'])
        ->and($topic->extras)->toBe(['subject_status' => 'Wajib', 'atp' => ['order_in_year' => 1]])
        ->and(Topic::query()->where('code', 'SIGRAK-TEST-2026-R1-BIG-K1-01')->sole()->only(['curation_notes', 'extras']))->toBe(['curation_notes' => null, 'extras' => null])
        ->and($topic->learningObjectives->pluck('code')->all())->toBe(['SIGRAK-TEST-2026-R1-MTK-K4-01-TP01'])
        ->and($topic->focusItems->pluck('estimated_jp')->all())->toBe([45, 45])
        ->and($report->mismatches())->toBe([
            'Topic SIGRAK-TEST-2026-R1-MAT-K4-02 uses subject code MAT; the catalogue code for Matematika is MTK.',
            'MTK grade 4 semester 2: topics add up to 80 JP, the allocation is 90 JP.',
        ])
        ->and($report->notes())->toContain('Koding dan Kecerdasan Artifisial grade 5: topics are kept as kokurikuler content (D-208).')
        ->and($report->notes())->toContain('Bahasa Inggris grade 1: optional content, off until a school switches it on (D-204).')
        ->and($report->notes())->toContain("The file says status 'active'; the version keeps status 'draft' until Tim Validasi SIGRAK signs it off (D-113).");
});

it('merges two files that share a version and keeps each file\'s curation', function (): void {
    importCatalogue();
    app(CurriculumImporter::class)->import(curriculumFixture('sigrak-topics.json'));
    $second = app(CurriculumImporter::class)->import(curriculumFixture('topics-with-mismatches.json'));
    $version = CurriculumVersion::query()->sole();

    expect($version->code)->toBe('SIGRAK-TEST-2026-R1')
        ->and(array_keys($version->meta['files']))->toBe(['tests/Fixtures/curriculum/sigrak-topics.json', 'tests/Fixtures/curriculum/topics-with-mismatches.json'])
        ->and(array_keys($version->meta['files']['tests/Fixtures/curriculum/topics-with-mismatches.json']['curation']))->toBe(['PAKR'])
        ->and(Topic::query()->whereBelongsTo($version)->count())->toBe(6)
        ->and(collect($second->mismatches())->filter(fn (string $line): bool => str_contains($line, 'not in the file'))->all())->toBe([]);

    // A second run of both files changes nothing.
    $again = app(CurriculumImporter::class)->import(curriculumFixture('sigrak-topics.json'))->changedRows()
        + app(CurriculumImporter::class)->import(curriculumFixture('topics-with-mismatches.json'))->changedRows();
    expect($again)->toBe(0);
});

it('keeps fokus–TP links a data task added when a file without links is imported again', function (): void {
    importCatalogue();
    app(CurriculumImporter::class)->import(curriculumFixture('sigrak-topics.json'));
    $focus = FocusItem::query()->where('code', 'SIGRAK-TEST-2026-R1-MAT-K4-02-FM01')->sole();
    $focus->learningObjectives()->attach(LearningObjective::query()->where('code', 'SIGRAK-TEST-2026-R1-MAT-K4-02-TP02')->value('id'));

    app(CurriculumImporter::class)->import(curriculumFixture('sigrak-topics.json'));

    expect($focus->learningObjectives()->pluck('code')->all())->toBe(['SIGRAK-TEST-2026-R1-MAT-K4-02-TP02']);
});

it('refuses the legacy app format and earlier draft datasets', function (Closure $json, string $message): void {
    importCatalogue();

    expect(fn () => app(CurriculumImporter::class)->import(curriculumTempFile($json())))->toThrow(InvalidCurriculumFile::class, $message)
        ->and(CurriculumVersion::query()->count())->toBe(0)
        ->and(Topic::query()->count())->toBe(0);
})->with([
    'legacy topic rows' => [fn (): array => [['ID_Kurikulum' => 'X-1', 'Mata Pelajaran' => 'Matematika']], 'the legacy app format is no longer imported'],
    'legacy SD allocation' => [fn (): array => ['versi_kurikulum' => 'X', 'alokasi' => [['kelas' => 4]]], 'the legacy app format is no longer imported'],
    'earlier draft dataset' => [fn (): array => [...curriculumJson(curriculumFixture('topics-with-mismatches.json')), 'dataset' => 'tp_atp_pendidikan_agama_sd'], "dataset 'tp_atp_pendidikan_agama_sd' is not a SIGRAK curriculum file"],
    'missing curation' => [function (): array {
        $json = curriculumJson(curriculumFixture('topics-with-mismatches.json'));
        unset($json['subjects'][0]['curation']);

        return $json;
    }, 'subjects[0].curation is missing'],
    'unknown curation status' => [function (): array {
        $json = curriculumJson(curriculumFixture('topics-with-mismatches.json'));
        $json['subjects'][0]['curation']['status'] = 'approved';

        return $json;
    }, "'approved' must be one of converted, in_review, curated"],
]);

it('keeps the D-234 Tata Boga trim in the SIGRAK data itself', function (): void {
    $this->artisan('curriculum:import')->assertSuccessful();
    $tataBoga = Topic::query()->where('code', 'SIGRAK-SD-2026-R1-KTB-K6-04')->sole();

    expect($tataBoga->only(['jp_total', 'week_start', 'week_end']))->toBe(['jp_total' => 28, 'week_start' => 1, 'week_end' => 14])
        ->and($tataBoga->weekly_distribution)->toBe(array_fill(0, 14, 2))
        ->and($tataBoga->focusItems()->pluck('estimated_jp')->all())->toBe([14, 14])
        ->and($tataBoga->extras)->not->toHaveKey('import_override')
        ->and(CurriculumVersion::query()->where('code', 'SIGRAK-SD-2026-R1')->sole()->meta['files'])->toHaveKeys([
            'docs/data/curriculum/sigrak-sd-2026-r1-agama.json',
            'docs/data/curriculum/sigrak-sd-2026-r1-umum.json',
        ]);
});

function sourceOverrideInput(int $jpTotal, int $weekEnd): TopicInput
{
    return new TopicInput(
        code: 'SIGRAK-TEST-2026-R1-KTB-K6-04', subject: new Subject, grade: 6, phase: Phase::C, semester: 2,
        elements: 'Wirausaha Bidang Boga', title: 'Wirausaha Bidang Boga', cpText: 'CP', atpCode: 'ATP-KTB-K6-BAB-04',
        orderInSemester: 2, jpTotal: $jpTotal, jpPerWeek: 2, weekStart: 1, weekEnd: $weekEnd, weeklyDistribution: array_fill(0, $weekEnd, 2),
        references: [], curationNotes: null, extras: null, objectives: [],
        focusItems: [['code' => 'SIGRAK-TEST-2026-R1-KTB-K6-04-FM01', 'order' => 1, 'title' => 'Fokus', 'estimated_jp' => $jpTotal, 'allocation_status' => '', 'objective_codes' => null]],
    );
}

it('applies a listed correction only while the source still has the values it was written for', function (): void {
    $overrides = new SourceOverrides([
        'SIGRAK-TEST-2026-R1-KTB-K6-04' => [
            'decision' => 'D-000', 'source' => ['jp_total' => 34, 'week_end' => 17], 'jp_total' => 28, 'week_end' => 14,
            'focus_jp' => ['SIGRAK-TEST-2026-R1-KTB-K6-04-FM01' => 28],
        ],
    ]);
    $report = new ImportReport('test.json');
    $trimmed = $overrides->apply(sourceOverrideInput(34, 17), $report);
    $fixed = sourceOverrideInput(28, 14);

    expect([$trimmed->jpTotal, $trimmed->weekEnd, count($trimmed->weeklyDistribution), $trimmed->focusItems[0]['estimated_jp']])->toBe([28, 14, 14, 28])
        ->and($trimmed->extras['import_override']['decision'] ?? null)->toBe('D-000')
        ->and($overrides->apply($fixed, $report))->toBe($fixed)
        ->and($report->notes())->toBe([
            'Topic SIGRAK-TEST-2026-R1-KTB-K6-04: trimmed from 34 JP over weeks 1–17 to 28 JP over weeks 1–14 (D-000).',
            'Topic SIGRAK-TEST-2026-R1-KTB-K6-04: the source no longer has 34 JP to week 17, so the D-000 correction is not applied.',
        ])
        ->and((new SourceOverrides)->apply($fixed, $report))->toBe($fixed);
});
