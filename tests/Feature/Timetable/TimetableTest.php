<?php

declare(strict_types=1);

use App\Actions\SchoolYear\ClassSubjects;
use App\Curriculum\Import\CurriculumImporter;
use App\Enums\MembershipRole;
use App\Enums\TimetableSlotKind;
use App\Models\AcademicYear;
use App\Models\Region;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolMembership;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\TimetableSlot;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Illuminate\Database\QueryException;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\travelTo;

beforeEach(function (): void {
    foreach (['docs/data/curriculum/subjects.json', 'docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json'] as $file) {
        app(CurriculumImporter::class)->import(base_path($file));
    }

    travelTo('2026-11-09');
    $this->school = School::factory()->create(['region_id' => Region::query()->where('regency', 'Kabupaten Kudus')->sole()->id]);
    $this->operator = User::factory()->operatorAt($this->school)->create();
    $this->teacher = User::factory()->teacherAt($this->school)->create(['name' => 'Bu Sari']);
    $this->year = AcademicYear::factory()->for($this->school)->starting(2026)->create();
    $this->class = SchoolClass::factory()->for($this->year)->create(['grade' => 4, 'name' => '4A']);
    $this->other = SchoolClass::factory()->for($this->year)->create(['grade' => 4, 'name' => '4B']);
});

function subject(string $code): Subject
{
    return Subject::query()->where('code', $code)->sole();
}

function teach(SchoolClass $class, string $code, User $teacher): void
{
    TeachingAssignment::factory()->create(['class_id' => $class->id, 'subject_id' => subject($code)->id, 'teacher_id' => $teacher->id]);
}

function allocation(SchoolClass $class, string $code): int
{
    return app(SchoolContext::class)->runFor($class->school_id, fn (): int => (int) app(ClassSubjects::class)->handle($class)
        ->first(fn ($offer): bool => $offer->subject->code === $code)?->jpPerWeek);
}

/**
 * @return array<string, mixed>
 */
function lessonInput(string $code, int $weekday = 1, string $from = '07:00', string $to = '08:10', int $jp = 2): array
{
    return ['weekday' => $weekday, 'starts_at' => $from, 'ends_at' => $to, 'kind' => 'lesson', 'subject' => $code, 'jp' => $jp];
}

/**
 * @return list<string> "period start kind" per row of a day
 */
function dayOf(SchoolClass $class, int $weekday): array
{
    return TimetableSlot::withoutSchoolScope()->where('class_id', $class->id)->where('weekday', $weekday)->orderBy('period_no')->get()
        ->map(fn (TimetableSlot $slot): string => $slot->period_no.' '.substr($slot->starts_at, 0, 5).' '.$slot->kind->value)
        ->all();
}

it('adds lessons and breaks, numbered by start time, with the teacher from the class (D-247, D-249)', function (): void {
    teach($this->class, 'MTK', $this->teacher);

    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('MTK', 1, '08:25', '09:35'))
        ->assertRedirect(route('classes.timetable.show', $this->class))
        ->assertSessionHas('toast', 'Jadwal tersimpan.');
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), ['weekday' => 1, 'starts_at' => '07:00', 'ends_at' => '07:30', 'kind' => 'ceremony'])
        ->assertSessionHasNoErrors();
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), ['weekday' => 1, 'starts_at' => '09:35', 'ends_at' => '09:50', 'kind' => 'break', 'label' => '  '])
        ->assertSessionHasNoErrors();

    expect(dayOf($this->class, 1))->toBe(['1 07:00 ceremony', '2 08:25 lesson', '3 09:35 break']);

    actingAs($this->operator)->get(route('classes.timetable.show', $this->class))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('School/Classes/Timetable')
            ->has('rows', 3)
            ->where('rows.0.kind_label', 'Upacara')
            ->where('rows.1.subject_name', subject('MTK')->name)
            ->where('rows.1.teacher_name', 'Bu Sari')
            ->where('rows.1.jp', 2)
            ->where('rows.1.clash', null)
            ->where('rows.2.label', null)
            ->where('is_complete', false)
            ->where('subjects', fn ($subjects): bool => collect($subjects)->firstWhere('code', 'MTK')['scheduled_jp'] === 2)
            ->where('translations', fn ($translations): bool => isset($translations['timetable.days.5'])));
});

it('validates the row', function (array $input, string $field): void {
    teach($this->class, 'MTK', $this->teacher);

    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), $input)->assertSessionHasErrors($field);

    expect(TimetableSlot::withoutSchoolScope()->count())->toBe(0);
})->with([
    'Sunday' => [lessonInput('MTK', 7), 'weekday'],
    'end before start' => [lessonInput('MTK', 1, '09:00', '08:00'), 'ends_at'],
    'unknown kind' => [['weekday' => 1, 'starts_at' => '07:00', 'ends_at' => '08:00', 'kind' => 'lunch'], 'kind'],
    'lesson without subject' => [['weekday' => 1, 'starts_at' => '07:00', 'ends_at' => '08:00', 'kind' => 'lesson', 'jp' => 1], 'subject'],
    'break with a subject' => [['weekday' => 1, 'starts_at' => '07:00', 'ends_at' => '08:00', 'kind' => 'break', 'subject' => 'MTK'], 'subject'],
    'jp too high' => [lessonInput('MTK', 1, '07:00', '08:10', 5), 'jp'],
    'subject the grade does not take' => [lessonInput('KODING'), 'subject'],
]);

it('refuses overlapping rows in a class', function (): void {
    TimetableSlot::factory()->for($this->class, 'schoolClass')->at(1, '07:00', '08:10')->create(['school_id' => $this->school->id]);

    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), ['weekday' => 1, 'starts_at' => '08:00', 'ends_at' => '08:20', 'kind' => 'break'])
        ->assertSessionHasErrors(['starts_at' => 'Waktunya bertabrakan dengan jam lain di hari yang sama.']);

    // Back to back is fine.
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), ['weekday' => 1, 'starts_at' => '08:10', 'ends_at' => '08:25', 'kind' => 'break'])
        ->assertSessionHasNoErrors();
});

it('refuses a subject without a teacher (D-249)', function (): void {
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('MTK'))
        ->assertSessionHasErrors(['subject' => subject('MTK')->name.' belum punya guru. Pilih gurunya dulu di halaman kelas.']);
});

it('refuses going over the allocation and is complete only when every subject matches (D-250)', function (): void {
    teach($this->class, 'PJOK', $this->teacher);
    $allocated = allocation($this->class, 'PJOK');
    expect($allocated)->toBeGreaterThan(1);

    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('PJOK', 2, '07:00', '09:00', $allocated - 1))->assertSessionHasNoErrors();
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('PJOK', 3, '07:00', '08:10', 2))
        ->assertSessionHasErrors(['jp' => 'Alokasi '.subject('PJOK')->name." {$allocated} JP per minggu. Sisa yang bisa dijadwalkan: 1 JP."]);
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('PJOK', 3, '07:00', '07:35', 1))->assertSessionHasNoErrors();

    actingAs($this->operator)->get(route('classes.timetable.show', $this->class))
        ->assertInertia(fn (Assert $page) => $page
            ->where('subjects', fn ($subjects): bool => collect($subjects)->firstWhere('code', 'PJOK')['status'] === 'complete')
            ->where('is_complete', false));

    // Changing a row counts the other rows only.
    $slot = TimetableSlot::withoutSchoolScope()->where('weekday', 3)->sole();
    actingAs($this->operator)->put(route('classes.timetable.update', [$this->class, $slot]), lessonInput('PJOK', 3, '07:00', '07:35', 1))->assertSessionHasNoErrors();
});

it('calls the timetable complete when every allocated subject matches', function (): void {
    $offers = app(SchoolContext::class)->runFor($this->school->id, fn () => app(ClassSubjects::class)->handle($this->class));
    $minute = 0;

    foreach ($offers as $offer) {
        if ($offer->jpPerWeek === null) {
            continue;
        }

        TeachingAssignment::factory()->create(['class_id' => $this->class->id, 'subject_id' => $offer->subject->id, 'teacher_id' => $this->teacher->id]);

        for ($left = $offer->jpPerWeek; $left > 0; $left -= 4) {
            $start = sprintf('%02d:%02d:00', 6 + intdiv($minute, 60), $minute % 60);
            $minute += 10;
            TimetableSlot::factory()->for($this->class, 'schoolClass')->lesson($offer->subject, min(4, $left))
                ->create(['school_id' => $this->school->id, 'weekday' => 1 + ($minute / 10) % 6, 'starts_at' => $start, 'ends_at' => sprintf('%02d:%02d:00', 6 + intdiv($minute, 60), $minute % 60)]);
        }
    }

    actingAs($this->operator)->get(route('classes.timetable.show', $this->class))
        ->assertInertia(fn (Assert $page) => $page->where('is_complete', true));
});

it('refuses a teacher clash with another class of the same year (D-248)', function (): void {
    teach($this->class, 'MTK', $this->teacher);
    teach($this->other, 'BIN', $this->teacher);
    TimetableSlot::factory()->for($this->other, 'schoolClass')->lesson(subject('BIN'))->at(2, '07:35', '08:45')->create(['school_id' => $this->school->id]);

    // Another year's timetable does not count.
    $oldClass = SchoolClass::factory()->for(AcademicYear::factory()->for($this->school)->starting(2025))->create(['grade' => 3, 'name' => '3A']);
    teach($oldClass, 'MTK', $this->teacher);
    TimetableSlot::factory()->for($oldClass, 'schoolClass')->lesson(subject('MTK'))->at(2, '10:00', '11:10')->create(['school_id' => $this->school->id]);

    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('MTK', 2, '07:00', '08:10'))
        ->assertSessionHasErrors(['starts_at' => 'Bu Sari sudah mengajar di kelas 4B pada hari Selasa jam 07:35–08:45.']);
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('MTK', 2, '10:00', '11:10'))->assertSessionHasNoErrors();
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('MTK', 2, '08:45', '09:20', 1))->assertSessionHasNoErrors();
});

it('refuses a guru mapel on the Kelas page who would clash (D-248)', function (): void {
    $other = User::factory()->teacherAt($this->school)->create(['name' => 'Pak Budi']);
    teach($this->class, 'MTK', $other);
    teach($this->other, 'BIN', $this->teacher);
    TimetableSlot::factory()->for($this->class, 'schoolClass')->lesson(subject('MTK'))->at(4, '07:00', '08:10')->create(['school_id' => $this->school->id]);
    TimetableSlot::factory()->for($this->other, 'schoolClass')->lesson(subject('BIN'))->at(4, '07:35', '08:45')->create(['school_id' => $this->school->id]);
    $membership = SchoolMembership::withoutSchoolScope()->where('user_id', $this->teacher->id)->value('public_id');

    actingAs($this->operator)->put(route('classes.assignments.update', $this->class), ['subject' => 'MTK', 'teacher' => $membership])
        ->assertSessionHasErrors(['teacher' => 'Bu Sari sudah mengajar di kelas 4B pada hari Kamis jam 07:35–08:45.']);

    expect(TeachingAssignment::withoutSchoolScope()->where('class_id', $this->class->id)->sole()->teacher_id)->toBe($other->id);
});

it('shows a clash left by a later teacher change as a warning', function (): void {
    teach($this->class, 'MTK', $this->teacher);
    teach($this->other, 'BIN', $this->teacher);
    TimetableSlot::factory()->for($this->class, 'schoolClass')->lesson(subject('MTK'))->at(4, '07:00', '08:10')->create(['school_id' => $this->school->id]);
    TimetableSlot::factory()->for($this->other, 'schoolClass')->lesson(subject('BIN'))->at(4, '07:35', '08:45')->create(['school_id' => $this->school->id]);

    actingAs($this->operator)->get(route('classes.timetable.show', $this->class))
        ->assertInertia(fn (Assert $page) => $page->where('rows.0.clash', 'Bu Sari sudah mengajar di kelas 4B pada hari Kamis jam 07:35–08:45.'));
});

it('renumbers both days when a row moves, and the day when a row is deleted', function (): void {
    $make = fn (int $day, string $from, string $to) => TimetableSlot::factory()->for($this->class, 'schoolClass')->at($day, $from, $to)->create(['school_id' => $this->school->id]);
    $first = $make(1, '07:00', '07:15');
    $make(1, '08:00', '08:15');
    $make(2, '09:00', '09:15');

    actingAs($this->operator)->put(route('classes.timetable.update', [$this->class, $first]), ['weekday' => 2, 'starts_at' => '07:00', 'ends_at' => '07:15', 'kind' => 'activity', 'label' => 'Senam'])
        ->assertSessionHasNoErrors();

    expect(dayOf($this->class, 1))->toBe(['1 08:00 break'])
        ->and(dayOf($this->class, 2))->toBe(['1 07:00 activity', '2 09:00 break'])
        ->and($first->refresh()->label)->toBe('Senam');

    actingAs($this->operator)->delete(route('classes.timetable.destroy', [$this->class, $first]))
        ->assertRedirect(route('classes.timetable.show', $this->class))
        ->assertSessionHas('toast', 'Jam dihapus dari jadwal.');

    expect(dayOf($this->class, 2))->toBe(['1 09:00 break']);
});

it('keeps the operator Jadwal to operators', function (MembershipRole $role): void {
    $slot = TimetableSlot::factory()->for($this->class, 'schoolClass')->create(['school_id' => $this->school->id]);
    $user = User::factory()->memberAt($this->school, $role)->create();

    actingAs($user)->get(route('classes.timetable.show', $this->class))->assertForbidden();
    actingAs($user)->post(route('classes.timetable.store', $this->class), ['weekday' => 1, 'starts_at' => '10:00', 'ends_at' => '10:15', 'kind' => 'break'])->assertForbidden();
    actingAs($user)->put(route('classes.timetable.update', [$this->class, $slot]), ['weekday' => 1, 'starts_at' => '10:00', 'ends_at' => '10:15', 'kind' => 'break'])->assertForbidden();
    actingAs($user)->delete(route('classes.timetable.destroy', [$this->class, $slot]))->assertForbidden();

    expect(TimetableSlot::withoutSchoolScope()->count())->toBe(1);
})->with([MembershipRole::Teacher, MembershipRole::Principal]);

it('hides another school\'s class and rows, and rows of another class (IDOR)', function (): void {
    $foreignClass = SchoolClass::factory()->for(AcademicYear::factory()->for(School::factory()))->create();
    $foreignSlot = TimetableSlot::factory()->for($foreignClass, 'schoolClass')->create(['school_id' => $foreignClass->school_id]);
    $otherClassSlot = TimetableSlot::factory()->for($this->other, 'schoolClass')->create(['school_id' => $this->school->id]);
    $break = ['weekday' => 1, 'starts_at' => '10:00', 'ends_at' => '10:15', 'kind' => 'break'];

    actingAs($this->operator)->get(route('classes.timetable.show', $foreignClass))->assertNotFound();
    actingAs($this->operator)->post(route('classes.timetable.store', $foreignClass), $break)->assertNotFound();
    actingAs($this->operator)->put(route('classes.timetable.update', [$foreignClass, $foreignSlot]), $break)->assertNotFound();
    actingAs($this->operator)->delete(route('classes.timetable.destroy', [$foreignClass, $foreignSlot]))->assertNotFound();
    actingAs($this->operator)->put(route('classes.timetable.update', [$this->class, $foreignSlot]), $break)->assertNotFound();
    actingAs($this->operator)->delete(route('classes.timetable.destroy', [$this->class, $otherClassSlot]))->assertNotFound();

    expect(TimetableSlot::withoutSchoolScope()->count())->toBe(2)
        ->and($foreignSlot->refresh()->starts_at)->toBe('09:00:00');
});

it('shows a teacher their lessons this year and their homeroom class (Kelas Saya > Jadwal)', function (): void {
    $colleague = User::factory()->teacherAt($this->school)->create(['name' => 'Pak Budi']);
    $this->class->update(['homeroom_teacher_id' => $this->teacher->id]);
    teach($this->class, 'MTK', $this->teacher);
    teach($this->class, 'PJOK', $colleague);
    teach($this->other, 'BIN', $this->teacher);
    TimetableSlot::factory()->for($this->class, 'schoolClass')->lesson(subject('MTK'))->at(1, '07:00', '08:10')->create(['school_id' => $this->school->id]);
    TimetableSlot::factory()->for($this->class, 'schoolClass')->lesson(subject('PJOK'), 3)->at(1, '08:25', '10:10')->create(['school_id' => $this->school->id]);
    TimetableSlot::factory()->for($this->other, 'schoolClass')->lesson(subject('BIN'))->at(1, '10:30', '11:40')->create(['school_id' => $this->school->id]);
    $oldClass = SchoolClass::factory()->for(AcademicYear::factory()->for($this->school)->starting(2025))->create(['grade' => 3, 'name' => '3A']);
    teach($oldClass, 'MTK', $this->teacher);
    TimetableSlot::factory()->for($oldClass, 'schoolClass')->lesson(subject('MTK'))->at(1, '07:00', '08:10')->create(['school_id' => $this->school->id]);

    actingAs($this->teacher)->get(route('my_classes.timetable'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Teacher/Timetable')
            ->where('year', '2026/2027')
            ->has('lessons', 2)
            ->where('lessons.0.class_name', '4A')
            ->where('lessons.1.class_name', '4B')
            ->where('total_jp', 4)
            ->has('homeroom_classes', 1)
            ->has('homeroom_classes.0.rows', 2)
            ->where('homeroom_classes.0.rows.1.teacher_name', 'Pak Budi'));

    actingAs($colleague)->get(route('my_classes.timetable'))
        ->assertInertia(fn (Assert $page) => $page->has('lessons', 1)->where('total_jp', 3)->has('homeroom_classes', 0));
});

it('does not show another school\'s lessons to a teacher', function (): void {
    $foreignSchool = School::factory()->create();
    $foreignClass = SchoolClass::factory()->for(AcademicYear::factory()->for($foreignSchool)->starting(2026))->create();
    TeachingAssignment::factory()->create(['class_id' => $foreignClass->id, 'subject_id' => subject('MTK')->id, 'teacher_id' => $this->teacher->id]);
    TimetableSlot::factory()->for($foreignClass, 'schoolClass')->lesson(subject('MTK'))->create(['school_id' => $foreignSchool->id]);

    actingAs($this->teacher)->get(route('my_classes.timetable'))
        ->assertInertia(fn (Assert $page) => $page->has('lessons', 0));
});

it('keeps Kelas Saya > Jadwal to teachers', function (MembershipRole $role): void {
    actingAs(User::factory()->memberAt($this->school, $role)->create())->get(route('my_classes.timetable'))->assertForbidden();
})->with([MembershipRole::Operator, MembershipRole::Principal]);

it('enforces the row rules in the database', function (): void {
    $base = ['class_id' => $this->class->id, 'school_id' => $this->school->id];

    expect(fn () => TimetableSlot::factory()->create([...$base, 'weekday' => 7]))->toThrow(QueryException::class)
        ->and(fn () => TimetableSlot::factory()->create([...$base, 'kind' => TimetableSlotKind::Lesson]))->toThrow(QueryException::class)
        ->and(fn () => TimetableSlot::factory()->lesson(subject('MTK'), 0)->create($base))->toThrow(QueryException::class)
        ->and(fn () => TimetableSlot::factory()->create([...$base, 'subject_id' => subject('MTK')->id]))->toThrow(QueryException::class)
        ->and(fn () => TimetableSlot::factory()->at(1, '09:00', '08:00')->create($base))->toThrow(QueryException::class);

    TimetableSlot::factory()->create([...$base, 'period_no' => 1]);
    expect(fn () => TimetableSlot::factory()->create([...$base, 'period_no' => 1]))->toThrow(QueryException::class);
});

it('lets different religion lessons share a time, and nothing else (D-251)', function (): void {
    $catholic = User::factory()->teacherAt($this->school)->create(['name' => 'Bu Maria']);
    teach($this->class, 'PAI', $this->teacher);
    teach($this->class, 'PAKT', $catholic);
    teach($this->class, 'MTK', $catholic);

    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('PAI', 3, '07:00', '08:45', 3))->assertSessionHasNoErrors();
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('PAKT', 3, '07:00', '08:45', 3))->assertSessionHasNoErrors();
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('MTK', 3, '08:00', '09:10'))
        ->assertSessionHasErrors(['starts_at' => 'Waktunya bertabrakan dengan jam lain di hari yang sama.']);
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), ['weekday' => 3, 'starts_at' => '08:30', 'ends_at' => '09:00', 'kind' => 'break'])
        ->assertSessionHasErrors('starts_at');

    expect(dayOf($this->class, 3))->toHaveCount(2);

    // The same religion twice at once is still an overlap.
    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('PAKT', 3, '08:00', '08:35', 1))
        ->assertSessionHasErrors(['starts_at' => 'Waktunya bertabrakan dengan jam lain di hari yang sama.']);
    TimetableSlot::withoutSchoolScope()->where('subject_id', subject('PAI')->id)->delete();

    actingAs($this->operator)->get(route('classes.timetable.show', $this->class))
        ->assertInertia(fn (Assert $page) => $page->where('subjects', function ($subjects): bool {
            $status = collect($subjects)->pluck('status', 'code');

            // PAI has a teacher but no rows; the four religions without a teacher are not counted.
            return $status['PAI'] === 'under' && $status['PAKT'] === 'complete' && $status['PAH'] === 'not_counted' && $status['PAKH'] === 'not_counted';
        }));
});

it('still checks each religion teacher for clashes', function (): void {
    teach($this->class, 'PAI', $this->teacher);
    teach($this->other, 'BIN', $this->teacher);
    TimetableSlot::factory()->for($this->other, 'schoolClass')->lesson(subject('BIN'))->at(3, '07:00', '08:10')->create(['school_id' => $this->school->id]);

    actingAs($this->operator)->post(route('classes.timetable.store', $this->class), lessonInput('PAI', 3, '07:35', '09:20', 3))
        ->assertSessionHasErrors(['starts_at' => 'Bu Sari sudah mengajar di kelas 4B pada hari Rabu jam 07:00–08:10.']);
});
