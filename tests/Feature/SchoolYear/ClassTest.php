<?php

declare(strict_types=1);

use App\Curriculum\Import\CurriculumImporter;
use App\Enums\MembershipRole;
use App\Models\AcademicYear;
use App\Models\Region;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolMembership;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\QueryException;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    foreach (['docs/data/curriculum/subjects.json', 'docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json'] as $file) {
        app(CurriculumImporter::class)->import(base_path($file));
    }

    // A Kudus school: SD national subjects plus Bahasa Jawa (region default, guru mapel).
    $this->school = School::factory()->create(['region_id' => Region::query()->where('regency', 'Kabupaten Kudus')->sole()->id]);
    $this->operator = User::factory()->operatorAt($this->school)->create();
    $this->year = AcademicYear::factory()->for($this->school)->starting(2026)->create();
});

function membershipOf(User $user): string
{
    return SchoolMembership::withoutSchoolScope()->where('user_id', $user->id)->where('role', MembershipRole::Teacher)->value('public_id');
}

/**
 * @return array<string, int> subject code => teacher id
 */
function assignmentsOf(SchoolClass $class): array
{
    return TeachingAssignment::withoutSchoolScope()->where('class_id', $class->id)->with('subject')->get()
        ->mapWithKeys(fn (TeachingAssignment $row): array => [$row->subject->code => $row->teacher_id])
        ->sortKeys()
        ->all();
}

// D-032: everything but PJOK, Bahasa Jawa, Bahasa Inggris, and the religions defaults to the class teacher.
const CLASS_TEACHER_SUBJECTS_GRADE_4 = ['BIN', 'IPAS', 'MTK', 'PP', 'SB'];

it('creates a class and gives the homeroom teacher the class-teacher subjects', function (): void {
    $homeroom = User::factory()->teacherAt($this->school)->create();

    actingAs($this->operator)->post(route('classes.store'), [
        'academic_year' => $this->year->public_id,
        'grade' => 4,
        'name' => ' 4A ',
        'homeroom_teacher' => membershipOf($homeroom),
    ])->assertSessionHasNoErrors();

    $class = SchoolClass::withoutSchoolScope()->sole();
    expect($class)
        ->school_id->toBe($this->school->id)
        ->academic_year_id->toBe($this->year->id)
        ->name->toBe('4A')
        ->homeroom_teacher_id->toBe($homeroom->id)
        ->and(assignmentsOf($class))->toBe(array_fill_keys(CLASS_TEACHER_SUBJECTS_GRADE_4, $homeroom->id));
});

it('creates a class without a homeroom teacher and without assignments', function (): void {
    actingAs($this->operator)->post(route('classes.store'), [
        'academic_year' => $this->year->public_id, 'grade' => 1, 'name' => '1A', 'homeroom_teacher' => null,
    ])->assertRedirect();

    expect(assignmentsOf(SchoolClass::withoutSchoolScope()->sole()))->toBe([]);
});

it('validates the class form', function (array $input, string $field): void {
    SchoolClass::factory()->for($this->year)->for($this->school)->create(['name' => '4A']);
    $otherSchoolTeacher = User::factory()->teacherAt(School::factory()->create())->create();
    $input = [...['academic_year' => $this->year->public_id, 'grade' => 4, 'name' => '4B'], ...$input];
    if (($input['homeroom_teacher'] ?? null) === 'other-school') {
        $input['homeroom_teacher'] = membershipOf($otherSchoolTeacher);
    }

    actingAs($this->operator)->post(route('classes.store'), $input)->assertSessionHasErrors($field);
    expect(SchoolClass::withoutSchoolScope()->count())->toBe(1);
})->with([
    'grade out of range' => [['grade' => 7], 'grade'],
    'missing name' => [['name' => ''], 'name'],
    'name taken in the year' => [['name' => '4A'], 'name'],
    'unknown year' => [['academic_year' => '01900000-0000-7000-8000-000000000000'], 'academic_year'],
    'teacher of another school' => [['homeroom_teacher' => 'other-school'], 'homeroom_teacher'],
]);

it('refuses another school\'s academic year', function (): void {
    $foreign = AcademicYear::factory()->for(School::factory())->create();

    actingAs($this->operator)->post(route('classes.store'), ['academic_year' => $foreign->public_id, 'grade' => 4, 'name' => '4A'])
        ->assertSessionHasErrors('academic_year');
});

it('lists the classes of the chosen year with their assignment progress', function (): void {
    $homeroom = User::factory()->teacherAt($this->school)->create(['name' => 'Bu Sari']);
    $older = AcademicYear::factory()->for($this->school)->starting(2025)->create();
    SchoolClass::factory()->for($older)->for($this->school)->create(['name' => '3A', 'grade' => 3]);
    SchoolClass::factory()->for(AcademicYear::factory()->for(School::factory()))->create(['name' => '4Z']);
    actingAs($this->operator)->post(route('classes.store'), [
        'academic_year' => $this->year->public_id, 'grade' => 4, 'name' => '4A', 'homeroom_teacher' => membershipOf($homeroom),
    ]);

    actingAs($this->operator)->get(route('classes.index', ['tahun' => $this->year->public_id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('School/Classes/Index')
            ->has('years', 2)
            ->where('selected_year', $this->year->public_id)
            ->has('classes', 1)
            ->where('classes.0.name', '4A')
            ->where('classes.0.homeroom_teacher', 'Bu Sari')
            ->where('classes.0.assigned_count', 5)
            ->where('classes.0.subjects_count', 14));

    actingAs($this->operator)->get(route('classes.index', ['tahun' => $older->public_id]))
        ->assertInertia(fn (Assert $page) => $page->has('classes', 1)->where('classes.0.name', '3A'));
});

it('shows a class with every subject and the school\'s teachers', function (): void {
    $homeroom = User::factory()->teacherAt($this->school)->create();
    User::factory()->teacherAt(School::factory()->create())->create();
    $class = SchoolClass::factory()->for($this->year)->for($this->school)->create(['grade' => 1, 'homeroom_teacher_id' => $homeroom->id]);

    actingAs($this->operator)->get(route('classes.show', $class))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('School/Classes/Show')
            ->where('school_class.id', $class->public_id)
            ->has('teachers', 1)
            ->where('teachers.0.id', membershipOf($homeroom))
            ->has('subjects', 12)
            ->where('subjects', fn ($subjects): bool => collect($subjects)->pluck('code')->contains('BJW')
                && collect($subjects)->every(fn (array $row): bool => $row['is_offered'])));
});

it('moves the old homeroom teacher\'s subjects to the new one and keeps other teachers', function (): void {
    [$old, $new, $specialist] = User::factory()->count(3)->sequence(fn () => [])->create()
        ->each(fn (User $teacher) => SchoolMembership::factory()->for($this->school)->for($teacher)->role(MembershipRole::Teacher)->create())
        ->all();
    actingAs($this->operator)->post(route('classes.store'), [
        'academic_year' => $this->year->public_id, 'grade' => 4, 'name' => '4A', 'homeroom_teacher' => membershipOf($old),
    ]);
    $class = SchoolClass::withoutSchoolScope()->sole();
    actingAs($this->operator)->put(route('classes.assignments.update', $class), ['subject' => 'SB', 'teacher' => membershipOf($specialist)]);

    actingAs($this->operator)->put(route('classes.update', $class), ['grade' => 4, 'name' => '4A', 'homeroom_teacher' => membershipOf($new)])
        ->assertRedirect(route('classes.show', $class));

    expect(assignmentsOf($class))->toBe(['BIN' => $new->id, 'IPAS' => $new->id, 'MTK' => $new->id, 'PP' => $new->id, 'SB' => $specialist->id]);

    actingAs($this->operator)->put(route('classes.update', $class), ['grade' => 4, 'name' => '4A', 'homeroom_teacher' => null]);
    expect(assignmentsOf($class))->toBe(['SB' => $specialist->id]);
});

it('drops the subjects a new grade does not take', function (): void {
    $homeroom = User::factory()->teacherAt($this->school)->create();
    actingAs($this->operator)->post(route('classes.store'), [
        'academic_year' => $this->year->public_id, 'grade' => 4, 'name' => '4A', 'homeroom_teacher' => membershipOf($homeroom),
    ]);
    $class = SchoolClass::withoutSchoolScope()->sole();

    actingAs($this->operator)->put(route('classes.update', $class), ['grade' => 1, 'name' => '1A', 'homeroom_teacher' => membershipOf($homeroom)]);

    expect(array_keys(assignmentsOf($class)))->toBe(['BIN', 'MTK', 'PP', 'SB']);
});

it('refuses a different academic year on update', function (): void {
    $class = SchoolClass::factory()->for($this->year)->for($this->school)->create();
    $other = AcademicYear::factory()->for($this->school)->starting(2027)->create();

    actingAs($this->operator)->put(route('classes.update', $class), ['academic_year' => $other->public_id, 'grade' => 4, 'name' => '4A'])
        ->assertSessionHasErrors('academic_year');
});

it('deletes a class with its assignments', function (): void {
    $class = SchoolClass::factory()->for($this->year)->for($this->school)->create();
    TeachingAssignment::factory()->for($class, 'schoolClass')->create(['school_id' => $this->school->id, 'subject_id' => Subject::query()->where('code', 'MTK')->value('id')]);

    actingAs($this->operator)->delete(route('classes.destroy', $class))
        ->assertRedirect(route('classes.index', ['tahun' => $this->year->public_id]));

    expect(SchoolClass::withoutSchoolScope()->count())->toBe(0)
        ->and(TeachingAssignment::withoutSchoolScope()->count())->toBe(0);
});

it('keeps Kelas to operators', function (MembershipRole $role): void {
    $class = SchoolClass::factory()->for($this->year)->for($this->school)->create();
    $user = User::factory()->memberAt($this->school, $role)->create();

    actingAs($user)->get(route('classes.index'))->assertForbidden();
    actingAs($user)->get(route('classes.create'))->assertForbidden();
    actingAs($user)->post(route('classes.store'), ['academic_year' => $this->year->public_id, 'grade' => 4, 'name' => '4B'])->assertForbidden();
    actingAs($user)->get(route('classes.show', $class))->assertForbidden();
    actingAs($user)->get(route('classes.edit', $class))->assertForbidden();
    actingAs($user)->put(route('classes.update', $class), ['grade' => 4, 'name' => '4B'])->assertForbidden();
    actingAs($user)->delete(route('classes.destroy', $class))->assertForbidden();
    actingAs($user)->put(route('classes.assignments.update', $class), ['subject' => 'MTK', 'teacher' => null])->assertForbidden();
})->with([MembershipRole::Teacher, MembershipRole::Principal]);

it('hides another school\'s class (IDOR)', function (): void {
    $class = SchoolClass::factory()->for(AcademicYear::factory()->for(School::factory()))->create(['name' => '4A']);

    actingAs($this->operator)->get(route('classes.show', $class))->assertNotFound();
    actingAs($this->operator)->get(route('classes.edit', $class))->assertNotFound();
    actingAs($this->operator)->put(route('classes.update', $class), ['grade' => 5, 'name' => '5A'])->assertNotFound();
    actingAs($this->operator)->delete(route('classes.destroy', $class))->assertNotFound();
    actingAs($this->operator)->put(route('classes.assignments.update', $class), ['subject' => 'MTK', 'teacher' => null])->assertNotFound();
    actingAs($this->operator)->get(route('classes.index', ['tahun' => $class->academicYear()->withoutGlobalScopes()->value('public_id')]))
        ->assertInertia(fn (Assert $page) => $page->has('classes', 0));

    expect($class->refresh()->name)->toBe('4A');
});

it('enforces the grade range and unique names in the database', function (): void {
    expect(fn () => SchoolClass::factory()->for($this->year)->for($this->school)->create(['grade' => 0]))->toThrow(QueryException::class);

    SchoolClass::factory()->for($this->year)->for($this->school)->create(['name' => '2A']);
    expect(fn () => SchoolClass::factory()->for($this->year)->for($this->school)->create(['name' => '2A']))->toThrow(QueryException::class);
});
