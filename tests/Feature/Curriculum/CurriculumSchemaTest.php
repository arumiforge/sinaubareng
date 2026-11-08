<?php

declare(strict_types=1);

use App\Models\Region;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

function curriculumVersionRow(?string $code = null): int
{
    $code ??= 'TEST-'.Str::upper(Str::random(8));

    return DB::table('curriculum_versions')->insertGetId([
        'code' => $code, 'name' => 'Test', 'status' => 'draft', 'meta' => '{}', 'imported_at' => now(),
    ]);
}

/**
 * @return array<string, mixed>
 */
function allocationRow(array $overrides = []): array
{
    return [
        'curriculum_version_id' => curriculumVersionRow(),
        'subject_id' => Subject::factory()->create()->id,
        'grade' => 1, 'phase' => 'A', 'learning_type' => 'intracurricular',
        'jp_per_week' => 4, 'weeks_per_year' => 36,
        's1_jp' => 72, 's1_effective_weeks' => 18, 's2_jp' => 72, 's2_effective_weeks' => 18,
        ...$overrides,
    ];
}

/**
 * @return array<string, mixed>
 */
function topicRow(array $overrides = []): array
{
    return [
        'curriculum_version_id' => curriculumVersionRow(), 'code' => 'T-1', 'legacy_codes' => '[]',
        'subject_id' => Subject::factory()->create()->id, 'grade' => 1, 'phase' => 'A', 'semester' => 1,
        'elements' => 'E', 'title' => 'T', 'cp_text' => 'CP', 'atp_code' => 'ATP-1', 'order_in_semester' => 1,
        'jp_total' => 6, 'jp_per_week' => 3, 'week_start' => 1, 'week_end' => 2,
        'weekly_distribution' => '[3,3]', 'references' => '{}',
        ...$overrides,
    ];
}

it('rejects values outside the fixed sets and ranges', function (string $table, Closure $row): void {
    expect(fn () => DB::table($table)->insert($row()))->toThrow(QueryException::class);
})->with([
    'version status' => ['curriculum_versions', fn (): array => [
        'code' => 'X-1', 'name' => 'X', 'status' => 'published', 'meta' => '{}', 'imported_at' => now(),
    ]],
    'subject kind' => ['subjects', fn (): array => [...Subject::factory()->raw(), 'kind' => 'elective', 'aliases' => '[]']],
    'subject teacher' => ['subjects', fn (): array => [...Subject::factory()->raw(), 'default_teacher' => 'principal', 'aliases' => '[]']],
    'subject religion' => ['subjects', fn (): array => [...Subject::factory()->raw(), 'religion' => 'kepercayaan', 'aliases' => '[]']],
    'subject level' => ['subjects', fn (): array => [...Subject::factory()->raw(), 'school_level' => 'smp', 'aliases' => '[]']],
    'allocation grade' => ['subject_allocations', fn (): array => allocationRow(['grade' => 7])],
    'allocation phase' => ['subject_allocations', fn (): array => allocationRow(['phase' => 'D'])],
    'allocation learning type' => ['subject_allocations', fn (): array => allocationRow(['learning_type' => 'extracurricular'])],
    'allocation weeks' => ['subject_allocations', fn (): array => allocationRow(['s2_effective_weeks' => 17])],
    'topic grade' => ['topics', fn (): array => topicRow(['grade' => 0])],
    'topic semester' => ['topics', fn (): array => topicRow(['semester' => 3])],
    'topic weeks' => ['topics', fn (): array => topicRow(['week_start' => 5, 'week_end' => 4])],
    'outcome phase' => ['learning_outcomes', fn (): array => [
        'curriculum_version_id' => curriculumVersionRow(), 'subject_id' => Subject::factory()->create()->id,
        'phase' => 'E', 'code' => 'CP-1', 'order' => 1, 'text' => 'x',
    ]],
    'region allocation' => ['region_subjects', fn (): array => [
        'region_id' => Region::factory()->create()->id, 'subject_id' => Subject::factory()->create()->id, 'allocation' => 0,
    ]],
    'school subject grade' => ['school_subjects', fn (): array => [
        'school_id' => School::factory()->create()->id, 'subject_id' => Subject::factory()->create()->id,
        'grade' => 7, 'enabled' => true, 'in_report' => false,
    ]],
]);

it('keeps one allocation per version, subject, grade, and learning type', function (): void {
    $row = allocationRow();
    DB::table('subject_allocations')->insert($row);

    expect(fn () => DB::table('subject_allocations')->insert($row))->toThrow(QueryException::class);
    DB::table('subject_allocations')->insert([...$row, 'learning_type' => 'cocurricular']);
    expect(DB::table('subject_allocations')->count())->toBe(2);
});

it('keeps codes unique', function (string $table, Closure $row, bool $ordered): void {
    $values = $row();
    DB::table($table)->insert($values);
    $vary = fn (int $n): array => $ordered ? ['order' => $n] : ['order_in_semester' => $n];

    // The same row under a new code is fine; the same code again is not.
    DB::table($table)->insert([...$values, ...$vary(2), 'code' => "{$values['code']}-B"]);
    expect(fn () => DB::table($table)->insert([...$values, ...$vary(3)]))->toThrow(QueryException::class);
    expect(DB::table($table)->count())->toBe(2);
})->with([
    'topic' => ['topics', fn (): array => topicRow(), false],
    'TP' => ['learning_objectives', fn (): array => [
        'topic_id' => DB::table('topics')->insertGetId(topicRow()), 'code' => 'TP-1', 'order' => 1, 'text' => 'x',
    ], true],
    'fokus' => ['focus_items', fn (): array => [
        'topic_id' => DB::table('topics')->insertGetId(topicRow()), 'code' => 'FM-1', 'order' => 1, 'title' => 'x',
        'estimated_jp' => 3, 'allocation_status' => 'sesuai',
    ], true],
]);

it('counts a school subject row for every grade once', function (): void {
    $school = School::factory()->create();
    $subject = Subject::factory()->create();
    $row = ['school_id' => $school->id, 'subject_id' => $subject->id, 'grade' => null, 'enabled' => true, 'in_report' => false];
    DB::table('school_subjects')->insert($row);

    expect(fn () => DB::table('school_subjects')->insert($row))->toThrow(QueryException::class);

    DB::table('school_subjects')->insert([...$row, 'grade' => 1]);
    expect(fn () => DB::table('school_subjects')->insert([...$row, 'grade' => 1]))->toThrow(QueryException::class);
});

it('removes TP, fokus, and their links with the topic', function (): void {
    $topicId = DB::table('topics')->insertGetId(topicRow());
    $objective = DB::table('learning_objectives')->insertGetId(['topic_id' => $topicId, 'code' => 'TP-1', 'order' => 1, 'text' => 'x']);
    $focus = DB::table('focus_items')->insertGetId(['topic_id' => $topicId, 'code' => 'FM-1', 'order' => 1, 'title' => 'x', 'estimated_jp' => 3, 'allocation_status' => 'sesuai']);
    DB::table('focus_item_objectives')->insert(['focus_item_id' => $focus, 'learning_objective_id' => $objective]);

    DB::table('topics')->delete($topicId);

    expect(DB::table('learning_objectives')->count())->toBe(0)
        ->and(DB::table('focus_items')->count())->toBe(0)
        ->and(DB::table('focus_item_objectives')->count())->toBe(0);
});

it('refuses to delete a subject that curriculum rows use', function (): void {
    $row = allocationRow();
    DB::table('subject_allocations')->insert($row);

    expect(fn () => DB::table('subjects')->delete($row['subject_id']))->toThrow(QueryException::class);
});

it('accepts the valid rows the constraint cases start from', function (): void {
    DB::table('subjects')->insert([...Subject::factory()->raw(), 'aliases' => '[]']);
    DB::table('subject_allocations')->insert(allocationRow());
    DB::table('topics')->insert(topicRow());

    expect(DB::table('subjects')->count())->toBe(3)
        ->and(DB::table('subject_allocations')->count())->toBe(1)
        ->and(DB::table('topics')->count())->toBe(1);
});
