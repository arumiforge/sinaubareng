<?php

declare(strict_types=1);

use App\Actions\SchoolYear\CopyClasses;
use App\Enums\MembershipRole;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    $this->school = School::factory()->create();
    $this->operator = User::factory()->operatorAt($this->school)->create();
    $this->teacher = User::factory()->teacherAt($this->school)->create();
    $this->previous = AcademicYear::factory()->for($this->school)->starting(2025)->create();
});

/**
 * @param  array<int, string>  $names  grade => comma-separated names
 */
function classesIn(AcademicYear $year, array $names, ?User $homeroom = null): void
{
    foreach ($names as $grade => $list) {
        foreach (explode(',', $list) as $name) {
            SchoolClass::factory()->for($year)->create(['grade' => $grade, 'name' => $name, 'homeroom_teacher_id' => $homeroom?->id]);
        }
    }
}

/**
 * @return list<string> "grade:name", sorted
 */
function namesIn(AcademicYear $year): array
{
    return SchoolClass::withoutSchoolScope()->where('academic_year_id', $year->id)->get()
        ->map(fn (SchoolClass $class): string => $class->grade.':'.$class->name)
        ->sort()->values()->all();
}

/**
 * @return list<array{grade: int, name: string, new_intake: bool}>
 */
function planFor(AcademicYear $source): array
{
    return app(SchoolContext::class)->runFor($source->school_id, fn (): array => app(CopyClasses::class)->plan($source));
}

function emptyYear(School $school, int $start = 2026): AcademicYear
{
    return AcademicYear::factory()->for($school)->starting($start)->create();
}

it('copies classes one grade up, skips grade 6, and remakes grade 1 (D-242…D-244)', function (): void {
    classesIn($this->previous, [1 => '1A,1B', 2 => '2A', 4 => '4 B,4-Umar', 6 => '6A'], $this->teacher);
    $target = emptyYear($this->school);

    actingAs($this->operator)->post(route('academic_years.copy_classes', $target), ['with_grade_one' => true])
        ->assertRedirect(route('classes.index', ['tahun' => $target->public_id]))
        ->assertSessionHas('toast', '7 kelas disalin ke tahun ajaran 2026/2027. Pilih wali kelas dan guru mapelnya.');

    expect(namesIn($target))->toBe(['1:1A', '1:1B', '2:2A', '2:2B', '3:3A', '5:5 B', '5:5-Umar'])
        ->and(SchoolClass::withoutSchoolScope()->where('academic_year_id', $target->id)->whereNotNull('homeroom_teacher_id')->count())->toBe(0)
        ->and(TeachingAssignment::withoutSchoolScope()->count())->toBe(0)
        ->and(namesIn($this->previous))->toHaveCount(6);
});

it('leaves grade 1 out when the operator unticks it (D-244)', function (): void {
    classesIn($this->previous, [1 => '1A', 3 => '3A']);
    $target = emptyYear($this->school);

    actingAs($this->operator)->post(route('academic_years.copy_classes', $target), ['with_grade_one' => false]);

    expect(namesIn($target))->toBe(['2:2A', '4:4A']);
});

it('keeps names without a leading grade number and settles clashes (D-245)', function (): void {
    // `5A` in grade 4 does not start with its grade, so it stays and clashes with 4A → 5A.
    classesIn($this->previous, [1 => 'Kelas Merah', 3 => '30A', 4 => '4A,5A']);

    $names = collect(planFor($this->previous))->map(fn (array $row): string => $row['grade'].':'.$row['name'])->all();

    expect($names)->toBe(['2:Kelas Merah', '4:30A', '5:5A', '5:5A 5', '1:Kelas Merah 1']);
});

it('settles a clash of a long name within the limit', function (): void {
    classesIn($this->previous, [1 => 'Kelas Pelangi Senja']);

    $names = collect(planFor($this->previous))->pluck('name')->all();

    expect($names)->toBe(['Kelas Pelangi Senja', 'Kelas Pelangi Senj 1']);
});

it('refuses a year that already has classes, so a second copy changes nothing (D-246)', function (): void {
    classesIn($this->previous, [2 => '2A']);
    $target = emptyYear($this->school);

    actingAs($this->operator)->post(route('academic_years.copy_classes', $target));
    actingAs($this->operator)->from(route('academic_years.index'))->post(route('academic_years.copy_classes', $target))
        ->assertRedirect(route('academic_years.index'))
        ->assertSessionHas('toast', 'Tahun ajaran 2026/2027 sudah punya kelas, jadi kelas tidak disalin.');

    expect(namesIn($target))->toBe(['3:3A']);
});

it('refuses when there is nothing to copy', function (array $names): void {
    classesIn($this->previous, $names);
    $target = emptyYear($this->school);

    actingAs($this->operator)->from(route('academic_years.index'))->post(route('academic_years.copy_classes', $target))
        ->assertSessionHas('toast', 'Tidak ada kelas yang bisa disalin dari tahun ajaran sebelumnya.');

    expect(namesIn($target))->toBe([]);
})->with([
    'no classes' => [[]],
    'only grade 6' => [[6 => '6A,6B']],
]);

it('copies from the latest year before the target, not a later one', function (): void {
    classesIn($this->previous, [2 => '2A']);
    $older = AcademicYear::factory()->for($this->school)->starting(2024)->create();
    classesIn($older, [4 => '4A']);
    $later = emptyYear($this->school, 2027);
    classesIn($later, [5 => '5Z']);
    $target = emptyYear($this->school, 2026);

    actingAs($this->operator)->post(route('academic_years.copy_classes', $target));

    expect(namesIn($target))->toBe(['3:3A']);
});

it('offers the copy on the create form and copies when the year is saved', function (): void {
    classesIn($this->previous, [1 => '1A', 5 => '5A']);

    actingAs($this->operator)->get(route('academic_years.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('copy_from.name', '2025/2026')
            ->has('copy_from.classes', 3)
            ->where('copy_from.classes.0', ['grade' => 2, 'name' => '2A', 'new_intake' => false])
            ->where('copy_from.classes.1', ['grade' => 6, 'name' => '6A', 'new_intake' => false])
            ->where('copy_from.classes.2', ['grade' => 1, 'name' => '1A', 'new_intake' => true])
            ->etc());

    actingAs($this->operator)->post(route('academic_years.store'), [
        'starts_on' => '2026-07-13', 'even_semester_starts_on' => '2027-01-04', 'ends_on' => '2027-06-26',
        'copy_classes' => true, 'with_grade_one' => true,
    ])->assertRedirect(route('academic_years.index'))
        ->assertSessionHas('toast', 'Tahun ajaran 2026/2027 tersimpan dengan 3 kelas. Pilih wali kelas dan guru mapelnya.');

    $target = AcademicYear::withoutSchoolScope()->where('name', '2026/2027')->sole();
    expect(namesIn($target))->toBe(['1:1A', '2:2A', '6:6A']);
});

it('creates the year without classes when the copy is unticked', function (): void {
    classesIn($this->previous, [2 => '2A']);

    actingAs($this->operator)->post(route('academic_years.store'), [
        'starts_on' => '2026-07-13', 'even_semester_starts_on' => '2027-01-04', 'ends_on' => '2027-06-26', 'copy_classes' => false,
    ])->assertSessionHas('toast', 'Tahun ajaran 2026/2027 tersimpan.');

    expect(SchoolClass::withoutSchoolScope()->count())->toBe(1);
});

it('shows the copy action only on empty years with something to copy', function (): void {
    classesIn($this->previous, [3 => '3A']);
    $target = emptyYear($this->school);

    actingAs($this->operator)->get(route('academic_years.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('years.0.id', $target->public_id)
            ->where('years.0.copy_from.name', '2025/2026')
            ->where('years.0.copy_from.classes', [['grade' => 4, 'name' => '4A', 'new_intake' => false]])
            ->where('years.1.copy_from', null)
            ->where('translations', fn ($translations): bool => isset($translations['classes.copy.actions.open'])));
});

it('keeps the copy to operators', function (MembershipRole $role): void {
    classesIn($this->previous, [2 => '2A']);
    $target = emptyYear($this->school);
    $user = User::factory()->memberAt($this->school, $role)->create();

    actingAs($user)->post(route('academic_years.copy_classes', $target))->assertForbidden();

    expect(namesIn($target))->toBe([]);
})->with([MembershipRole::Teacher, MembershipRole::Principal]);

it('cannot copy into another school\'s year (IDOR)', function (): void {
    $other = School::factory()->create();
    $otherYear = emptyYear($other);
    classesIn(AcademicYear::factory()->for($other)->starting(2025)->create(), [2 => '2A']);

    actingAs($this->operator)->post(route('academic_years.copy_classes', $otherYear))->assertNotFound();

    expect(namesIn($otherYear))->toBe([]);
});

it('cannot copy another school\'s classes (IDOR)', function (): void {
    classesIn(AcademicYear::factory()->for(School::factory())->starting(2025)->create(), [2 => '2A']);
    $target = emptyYear($this->school);

    actingAs($this->operator)->from(route('academic_years.index'))->post(route('academic_years.copy_classes', $target))
        ->assertSessionHas('toast', 'Tidak ada kelas yang bisa disalin dari tahun ajaran sebelumnya.');

    expect(namesIn($target))->toBe([]);
});
