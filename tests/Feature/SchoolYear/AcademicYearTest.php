<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\QueryException;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\travelTo;

function yearOperator(School $school): User
{
    return User::factory()->operatorAt($school)->create();
}

/**
 * @return array{starts_on: string, even_semester_starts_on: string, ends_on: string}
 */
function yearDates(int $year): array
{
    return ['starts_on' => "{$year}-07-13", 'even_semester_starts_on' => ($year + 1).'-01-05', 'ends_on' => ($year + 1).'-06-26'];
}

it('lists the school\'s years, newest first, marking the current one', function (): void {
    travelTo('2026-11-08');
    $school = School::factory()->create();
    AcademicYear::factory()->for($school)->starting(2025)->create();
    $current = AcademicYear::factory()->for($school)->starting(2026)->create();
    SchoolClass::factory()->for($current)->for($school)->create();
    AcademicYear::factory()->for(School::factory())->starting(2027)->create();

    actingAs(yearOperator($school))->get(route('academic_years.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('School/AcademicYears/Index')
            ->has('years', 2)
            ->where('years.0.name', '2026/2027')
            ->where('years.0.is_current', true)
            ->where('years.0.classes_count', 1)
            ->where('years.0.odd_semester', '01/07/2026 – 31/12/2026')
            ->where('years.0.even_semester', '01/01/2027 – 30/06/2027')
            ->where('years.1.is_current', false)
            ->where('translations', fn ($translations): bool => isset($translations['classes.years.titles.index'])));
});

it('suggests the year after the latest one', function (): void {
    $school = School::factory()->create();
    AcademicYear::factory()->for($school)->starting(2026)->create();

    actingAs(yearOperator($school))->get(route('academic_years.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('School/AcademicYears/Form')
            ->where('defaults.starts_on', '2027-07-01')
            ->where('defaults.even_semester_starts_on', '2028-01-01')
            ->where('defaults.ends_on', '2028-06-30'));
});

it('creates a year named after its start date', function (): void {
    $school = School::factory()->create();

    actingAs(yearOperator($school))->post(route('academic_years.store'), yearDates(2026))
        ->assertRedirect(route('academic_years.index'))
        ->assertSessionHas('toast');

    expect(AcademicYear::withoutSchoolScope()->sole())
        ->school_id->toBe($school->id)
        ->name->toBe('2026/2027')
        ->even_semester_starts_on->toDateString()->toBe('2027-01-05');
});

it('validates the semester dates', function (array $dates, string $field): void {
    $school = School::factory()->create();
    AcademicYear::factory()->for($school)->starting(2025)->create();

    actingAs(yearOperator($school))->post(route('academic_years.store'), [...yearDates(2026), ...$dates])
        ->assertSessionHasErrors($field);

    expect(AcademicYear::withoutSchoolScope()->count())->toBe(1);
})->with([
    'missing date' => [['ends_on' => ''], 'ends_on'],
    'even semester before the start' => [['even_semester_starts_on' => '2026-07-01'], 'even_semester_starts_on'],
    'end before the even semester' => [['ends_on' => '2026-12-31'], 'ends_on'],
    'longer than a year' => [['ends_on' => '2027-07-13'], 'ends_on'],
    'not a date' => [['starts_on' => '13/07/2026'], 'starts_on'],
    'same name as another year' => [['starts_on' => '2025-12-01', 'even_semester_starts_on' => '2026-01-05', 'ends_on' => '2026-06-30'], 'starts_on'],
    'overlaps another year' => [['starts_on' => '2026-06-01', 'ends_on' => '2027-05-31'], 'starts_on'],
]);

it('lets another school use the same year name', function (): void {
    AcademicYear::factory()->for(School::factory())->starting(2026)->create();
    $school = School::factory()->create();

    actingAs(yearOperator($school))->post(route('academic_years.store'), yearDates(2026))->assertSessionHasNoErrors();
});

it('changes a year\'s dates and keeps its own name free', function (): void {
    $school = School::factory()->create();
    $year = AcademicYear::factory()->for($school)->starting(2026)->create();

    actingAs(yearOperator($school))->put(route('academic_years.update', $year), yearDates(2026))
        ->assertRedirect(route('academic_years.index'));

    expect($year->refresh()->starts_on->toDateString())->toBe('2026-07-13');
});

it('deletes a year without classes, and keeps one with classes', function (): void {
    $school = School::factory()->create();
    $operator = yearOperator($school);
    $empty = AcademicYear::factory()->for($school)->starting(2025)->create();
    $used = AcademicYear::factory()->for($school)->starting(2026)->create();
    SchoolClass::factory()->for($used)->for($school)->create();

    actingAs($operator)->delete(route('academic_years.destroy', $empty))->assertRedirect(route('academic_years.index'));
    actingAs($operator)->from(route('academic_years.index'))->delete(route('academic_years.destroy', $used))
        ->assertRedirect(route('academic_years.index'))
        ->assertSessionHas('toast', __('classes.years.errors.has_classes', ['name' => '2026/2027']));

    expect(AcademicYear::withoutSchoolScope()->pluck('id')->all())->toBe([$used->id]);
});

it('keeps Tahun Ajaran to operators', function (MembershipRole $role): void {
    $school = School::factory()->create();
    $year = AcademicYear::factory()->for($school)->create();
    $user = User::factory()->memberAt($school, $role)->create();

    actingAs($user)->get(route('academic_years.index'))->assertForbidden();
    actingAs($user)->get(route('academic_years.create'))->assertForbidden();
    actingAs($user)->post(route('academic_years.store'), yearDates(2027))->assertForbidden();
    actingAs($user)->get(route('academic_years.edit', $year))->assertForbidden();
    actingAs($user)->put(route('academic_years.update', $year), yearDates(2026))->assertForbidden();
    actingAs($user)->delete(route('academic_years.destroy', $year))->assertForbidden();
})->with([MembershipRole::Teacher, MembershipRole::Principal]);

it('hides another school\'s year (IDOR)', function (): void {
    $year = AcademicYear::factory()->for(School::factory())->create();
    $operator = yearOperator(School::factory()->create());

    actingAs($operator)->get(route('academic_years.edit', $year))->assertNotFound();
    actingAs($operator)->put(route('academic_years.update', $year), yearDates(2026))->assertNotFound();
    actingAs($operator)->delete(route('academic_years.destroy', $year))->assertNotFound();

    expect($year->refresh()->starts_on->toDateString())->toBe('2026-07-01');
});

it('enforces the semester order in the database', function (): void {
    expect(fn () => AcademicYear::factory()->create(['even_semester_starts_on' => '2026-06-01']))
        ->toThrow(QueryException::class);
});
