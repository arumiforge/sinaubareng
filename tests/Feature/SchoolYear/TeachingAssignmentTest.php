<?php

declare(strict_types=1);

use App\Curriculum\Import\CurriculumImporter;
use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolMembership;
use App\Models\SchoolSubject;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Illuminate\Database\QueryException;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    foreach (['docs/data/curriculum/subjects.json', 'docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json'] as $file) {
        app(CurriculumImporter::class)->import(base_path($file));
    }

    $this->school = School::factory()->create();
    $this->operator = User::factory()->operatorAt($this->school)->create();
    $this->class = SchoolClass::factory()->for(AcademicYear::factory()->for($this->school))->create(['grade' => 4]);
    $this->teacher = User::factory()->teacherAt($this->school)->create();
    $this->membership = SchoolMembership::withoutSchoolScope()->where('user_id', $this->teacher->id)->value('public_id');
});

function teacherOf(SchoolClass $class, string $code): ?int
{
    return TeachingAssignment::withoutSchoolScope()->where('class_id', $class->id)
        ->where('subject_id', Subject::query()->where('code', $code)->value('id'))
        ->value('teacher_id');
}

it('assigns a guru mapel, reassigns, and clears a subject', function (): void {
    $other = User::factory()->teacherAt($this->school)->create();
    $otherMembership = SchoolMembership::withoutSchoolScope()->where('user_id', $other->id)->value('public_id');

    actingAs($this->operator)->put(route('classes.assignments.update', $this->class), ['subject' => 'PJOK', 'teacher' => $this->membership])
        ->assertRedirect(route('classes.show', $this->class))
        ->assertSessionHas('toast');
    expect(teacherOf($this->class, 'PJOK'))->toBe($this->teacher->id);

    actingAs($this->operator)->put(route('classes.assignments.update', $this->class), ['subject' => 'PJOK', 'teacher' => $otherMembership]);
    expect(teacherOf($this->class, 'PJOK'))->toBe($other->id)
        ->and(TeachingAssignment::withoutSchoolScope()->count())->toBe(1);

    actingAs($this->operator)->put(route('classes.assignments.update', $this->class), ['subject' => 'PJOK', 'teacher' => null]);
    expect(teacherOf($this->class, 'PJOK'))->toBeNull();
});

it('validates the subject and the teacher', function (array $input, string $field): void {
    $ended = User::factory()->create();
    SchoolMembership::factory()->for($this->school)->for($ended)->role(MembershipRole::Teacher)->create(['status' => MembershipStatus::Ended, 'ended_on' => now()]);
    $foreign = User::factory()->teacherAt(School::factory()->create())->create();
    $input['teacher'] = match ($input['teacher'] ?? 'mine') {
        'ended' => SchoolMembership::withoutSchoolScope()->where('user_id', $ended->id)->value('public_id'),
        'foreign' => SchoolMembership::withoutSchoolScope()->where('user_id', $foreign->id)->value('public_id'),
        'mine' => $this->membership,
        default => $input['teacher'],
    };

    actingAs($this->operator)->put(route('classes.assignments.update', $this->class), $input)->assertSessionHasErrors($field);
    expect(TeachingAssignment::withoutSchoolScope()->count())->toBe(0);
})->with([
    'unknown subject' => [['subject' => 'XYZ'], 'subject'],
    'subject the grade does not take' => [['subject' => 'KKA'], 'subject'],
    'missing subject' => [['subject' => ''], 'subject'],
    'teacher who left' => [['subject' => 'MTK', 'teacher' => 'ended'], 'teacher'],
    'teacher of another school' => [['subject' => 'MTK', 'teacher' => 'foreign'], 'teacher'],
    'not a teacher id' => [['subject' => 'MTK', 'teacher' => 'abc'], 'teacher'],
]);

it('assigns optional content the school switched on, and lets a switched-off subject be cleared', function (): void {
    app(SchoolContext::class)->runFor($this->school->id, fn () => SchoolSubject::query()->create([
        'subject_id' => Subject::query()->where('code', 'KTB')->value('id'), 'grade' => null, 'enabled' => true, 'in_report' => false,
    ]));

    actingAs($this->operator)->put(route('classes.assignments.update', $this->class), ['subject' => 'KTB', 'teacher' => $this->membership])
        ->assertSessionHasNoErrors();

    actingAs($this->operator)->get(route('classes.show', $this->class))
        ->assertInertia(fn (Assert $page) => $page->where('subjects', fn ($subjects): bool => collect($subjects)
            ->contains(fn (array $row): bool => $row['code'] === 'KTB' && $row['is_optional'] && ! $row['in_report'] && $row['teacher'] === $this->membership)));

    SchoolSubject::withoutSchoolScope()->update(['enabled' => false]);

    actingAs($this->operator)->get(route('classes.show', $this->class))
        ->assertInertia(fn (Assert $page) => $page->where('subjects', fn ($subjects): bool => collect($subjects)
            ->contains(fn (array $row): bool => $row['code'] === 'KTB' && ! $row['is_offered'])));
    actingAs($this->operator)->put(route('classes.assignments.update', $this->class), ['subject' => 'KTB', 'teacher' => $this->membership])
        ->assertSessionHasErrors('subject');
    actingAs($this->operator)->put(route('classes.assignments.update', $this->class), ['subject' => 'KTB', 'teacher' => null])
        ->assertSessionHasNoErrors();

    expect(teacherOf($this->class, 'KTB'))->toBeNull();
});

it('shows a teacher who left by name, without an active choice', function (): void {
    TeachingAssignment::factory()->for($this->class, 'schoolClass')->create([
        'school_id' => $this->school->id, 'subject_id' => Subject::query()->where('code', 'MTK')->value('id'), 'teacher_id' => $this->teacher->id,
    ]);
    SchoolMembership::withoutSchoolScope()->where('user_id', $this->teacher->id)->update(['status' => MembershipStatus::Ended, 'ended_on' => now()]);

    actingAs($this->operator)->get(route('classes.show', $this->class))
        ->assertInertia(fn (Assert $page) => $page
            ->has('teachers', 0)
            ->where('subjects', fn ($subjects): bool => collect($subjects)->contains(fn (array $row): bool => $row['code'] === 'MTK'
                && $row['teacher'] === null && $row['teacher_name'] === $this->teacher->name && ! $row['teacher_is_active'])));
});

it('allows one teacher per subject and class in the database', function (): void {
    $subjectId = Subject::query()->where('code', 'MTK')->value('id');
    $row = ['school_id' => $this->school->id, 'subject_id' => $subjectId];
    TeachingAssignment::factory()->for($this->class, 'schoolClass')->create($row);

    expect(fn () => TeachingAssignment::factory()->for($this->class, 'schoolClass')->create($row))->toThrow(QueryException::class);
});
