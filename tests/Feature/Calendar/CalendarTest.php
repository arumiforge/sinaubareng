<?php

declare(strict_types=1);

use App\Calendar\DayCategory;
use App\Calendar\EffectiveDays;
use App\Calendar\Impact\CalendarImpact;
use App\Calendar\Impact\MeetingImpact;
use App\Calendar\NationalHolidays;
use App\Enums\CalendarEventKind;
use App\Enums\CalendarStatus;
use App\Models\AcademicYear;
use App\Models\CalendarChangeRequest;
use App\Models\NationalHoliday;
use App\Models\Region;
use App\Models\RegionCalendar;
use App\Models\School;
use App\Models\SchoolCalendar;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\artisan;
use function Pest\Laravel\travelTo;

const HOLIDAYS = 'docs/data/calendar/national-holidays-2026-2027.json';
const JATENG = 'docs/data/calendar/region-jawa-tengah-2026-2027.json';

beforeEach(function (): void {
    travelTo('2026-11-09');
    artisan('calendar:import-holidays', ['file' => base_path(HOLIDAYS)])->assertSuccessful();
    artisan('calendar:import-region', ['file' => base_path(JATENG)])->assertSuccessful();
    RegionCalendar::query()->update(['status' => CalendarStatus::Active->value]);

    $kudus = Region::query()->firstOrCreate(['province' => 'Jawa Tengah', 'regency' => 'Kabupaten Kudus']);
    $this->school = School::factory()->create(['region_id' => $kudus->id]);
    $this->operator = User::factory()->operatorAt($this->school)->create();
    $this->teacher = User::factory()->teacherAt($this->school)->create();
    // The Jawa Tengah 6-day year (D-323): 13 July 2026 to 19 June 2027, semester 2 from 2 January.
    $this->year = AcademicYear::factory()->for($this->school)->create([
        'name' => '2026/2027', 'starts_on' => '2026-07-13', 'even_semester_starts_on' => '2027-01-02', 'ends_on' => '2027-06-19',
    ]);
    $this->class = SchoolClass::factory()->for($this->year)->create(['grade' => 4, 'name' => '4A', 'homeroom_teacher_id' => $this->teacher->id]);
    $this->other = SchoolClass::factory()->for($this->year)->create(['grade' => 5, 'name' => '5A']);
});

function inSchool(School $school, callable $callback): mixed
{
    return app(SchoolContext::class)->runFor($school->id, $callback);
}

function copyCalendar(User $operator, AcademicYear $year): SchoolCalendar
{
    actingAs($operator)->post(route('calendar.store'), ['tahun' => $year->public_id])->assertSessionHasNoErrors();

    return SchoolCalendar::withoutSchoolScope()->where('school_id', $year->school_id)->sole();
}

/**
 * @return array<string, mixed>
 */
function eventInput(array $overrides = []): array
{
    return ['title' => 'Karyawisata', 'kind' => 'no_lessons', 'starts_on' => '2026-11-18', 'ends_on' => '2026-11-18', ...$overrides];
}

/**
 * The national holiday dates of the data file within a range, in order.
 *
 * @return list<string>
 */
function holidayDates(string $from, string $to): array
{
    $data = json_decode((string) file_get_contents(base_path(HOLIDAYS)), true);

    return collect($data['academic_years'])->flatMap(fn (array $year): array => array_column($year['days'], 'date'))
        ->filter(fn (string $date): bool => $date >= $from && $date <= $to)->sort()->values()->all();
}

function effective(SchoolClass $class): array
{
    return inSchool($class->school, fn (): array => app(EffectiveDays::class)->forClass($class->fresh())->toArray());
}

it('imports holidays and the region calendar idempotently (D-253, D-256)', function (): void {
    expect(NationalHoliday::query()->count())->toBe(30);
    $calendar = RegionCalendar::query()->sole();
    [$version, $events] = [$calendar->version, $calendar->events()->pluck('public_id')->sort()->values()->all()];

    artisan('calendar:import-holidays', ['file' => base_path(HOLIDAYS)])->expectsOutputToContain('0 baru, 0 diubah, 30 tetap')->assertSuccessful();
    artisan('calendar:import-region', ['file' => base_path(JATENG)])->assertSuccessful();

    $calendar->refresh();
    expect($calendar->version)->toBe($version)
        ->and($calendar->events()->pluck('public_id')->sort()->values()->all())->toBe($events)
        ->and($calendar->region->regency)->toBeNull()
        ->and(NationalHoliday::query()->where('date', '2026-12-24')->sole()->kind->value)->toBe('joint_leave');
});

it('gives 254 effective days, 134 and 120 per semester, for the Jawa Tengah 6-day calendar (D-259, D-323)', function (): void {
    copyCalendar($this->operator, $this->year);

    $table = effective($this->class);

    expect($table['year']['effective_days'])->toBe(254)
        ->and($table['semesters'][1]['effective_days'])->toBe(134)
        ->and($table['semesters'][2]['effective_days'])->toBe(120)
        ->and($table['days_per_week'])->toBe(6)
        ->and($table['from_timetable'])->toBeFalse()
        ->and(round($table['year']['effective_weeks'], 4))->toBe(round(254 / 6, 4));
});

it('copies the province calendar for a Kudus school and shows it on the page (D-256, D-323)', function (): void {
    actingAs($this->operator)->get(route('calendar.show'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('School/Calendar/Show')->where('calendar', null)->whereNot('region_calendar', null)->where('can_manage', true));

    $calendar = copyCalendar($this->operator, $this->year);

    expect($calendar->source_calendar_id)->toBe(RegionCalendar::query()->sole()->id)
        ->and($calendar->events()->count())->toBe(RegionCalendar::query()->sole()->events()->count())
        ->and($calendar->joint_leave_school_days)->toContain('2027-02-05');

    actingAs($this->operator)->get(route('calendar.show'))
        ->assertInertia(fn (Assert $page) => $page->where('calendar.region_changes', [])
            ->where('holidays', fn ($holidays): bool => expect(collect($holidays)->pluck('date')->all())->toBe(holidayDates('2026-07-13', '2027-06-19')) !== null)
            ->where('effective_days.year.effective_days', 254));
});

it('offers later region changes, then applies or skips them (D-256)', function (): void {
    $calendar = copyCalendar($this->operator, $this->year);
    $region = RegionCalendar::query()->sole();
    $region->events()->create(['kind' => CalendarEventKind::Holiday, 'title' => 'Libur tambahan', 'starts_on' => '2026-11-20', 'ends_on' => '2026-11-20']);
    $region->update(['joint_leave_school_days' => ['2027-02-05']]);

    actingAs($this->operator)->get(route('calendar.show'))
        ->assertInertia(fn (Assert $page) => $page->has('calendar.region_changes', 5));

    actingAs($this->operator)->post(route('calendar.region_changes', $calendar), ['action' => 'apply'])->assertSessionHas('toast', 'Perubahan kalender wilayah diterapkan.');

    $calendar->refresh();
    expect($calendar->events()->where('title', 'Libur tambahan')->exists())->toBeTrue()
        ->and($calendar->joint_leave_school_days)->toBe(['2027-02-05'])
        ->and($calendar->source_version)->toBe($region->fresh()->version)
        ->and(effective($this->class)['year']['effective_days'])->toBe(254 - 1 - 4);

    $region->events()->where('title', 'Libur tambahan')->sole()->delete();
    actingAs($this->operator)->post(route('calendar.region_changes', $calendar), ['action' => 'skip'])->assertSessionHas('toast', 'Perubahan kalender wilayah dilewati.');
    expect($calendar->events()->where('title', 'Libur tambahan')->exists())->toBeTrue();
    actingAs($this->operator)->get(route('calendar.show'))->assertInertia(fn (Assert $page) => $page->where('calendar.region_changes', []));
});

it('makes an empty calendar when the region has none, and shows only active region calendars', function (): void {
    RegionCalendar::query()->update(['status' => CalendarStatus::Draft->value]);

    actingAs($this->operator)->get(route('calendar.show'))->assertInertia(fn (Assert $page) => $page->where('region_calendar', null));
    $calendar = copyCalendar($this->operator, $this->year);

    expect($calendar->source_calendar_id)->toBeNull()->and($calendar->events()->count())->toBe(0);
});

it('limits a class event to that class (D-258)', function (): void {
    $calendar = copyCalendar($this->operator, $this->year);

    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput(['class' => $this->class->public_id]))
        ->assertSessionHasNoErrors()->assertSessionHas('toast', 'Kalender tersimpan.');

    expect(effective($this->class)['year']['effective_days'])->toBe(253)
        ->and(effective($this->other)['year']['effective_days'])->toBe(254);
});

it('counts only the class timetable days', function (): void {
    copyCalendar($this->operator, $this->year);
    foreach ([1, 2, 3, 4, 5] as $weekday) {
        DB::table('timetable_slots')->insert(['public_id' => (string) str()->uuid(), 'school_id' => $this->school->id, 'class_id' => $this->class->id, 'weekday' => $weekday, 'period_no' => 1, 'starts_at' => '07:00', 'ends_at' => '07:30', 'kind' => 'lesson', 'subject_id' => Subject::factory()->create()->id, 'jp' => 1]);
    }

    $table = effective($this->class);

    expect($table['from_timetable'])->toBeTrue()->and($table['days_per_week'])->toBe(5)
        ->and($table['year']['effective_days'])->toBeLessThan(254)
        ->and($table['year']['effective_weeks'])->toBe($table['year']['effective_days'] / 5);
});

it('keeps a cuti bersama day as a school day or a holiday (D-255)', function (): void {
    $calendar = copyCalendar($this->operator, $this->year);

    actingAs($this->operator)->put(route('calendar.joint_leave', $calendar), ['date' => '2027-03-09', 'school_day' => true])->assertSessionHasNoErrors();
    expect(effective($this->class)['year']['effective_days'])->toBe(255);

    actingAs($this->operator)->put(route('calendar.joint_leave', $calendar), ['date' => '2027-02-05', 'school_day' => false])->assertSessionHasNoErrors();
    expect(effective($this->class)['year']['effective_days'])->toBe(254)
        ->and($calendar->fresh()->joint_leave_school_days)->toContain('2027-03-09')->not->toContain('2027-02-05');

    actingAs($this->operator)->put(route('calendar.joint_leave', $calendar), ['date' => '2026-12-25', 'school_day' => true])->assertSessionHasErrors('date');
});

it('refuses STS or SAS on a holiday and warns about Sundays (D-260)', function (): void {
    $calendar = copyCalendar($this->operator, $this->year);

    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput(['kind' => 'exam', 'exam_type' => 'sas', 'starts_on' => '2026-12-23', 'ends_on' => '2026-12-25']))
        ->assertSessionHasErrors(['starts_on' => 'SAS tidak bisa dijadwalkan pada Jumat, 25 Desember 2026 karena Kelahiran Yesus Kristus.']);

    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput(['kind' => 'exam', 'exam_type' => 'other', 'starts_on' => '2026-12-25', 'ends_on' => '2026-12-25']))
        ->assertSessionHasNoErrors();

    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput(['kind' => 'school_event', 'starts_on' => '2026-11-14', 'ends_on' => '2026-11-15']))
        ->assertSessionHasNoErrors()
        ->assertSessionHas('toast', 'Kalender tersimpan. Perhatian: Acara ini mengenai hari Minggu.');

    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput(['starts_on' => '2027-07-01', 'ends_on' => '2027-07-01']))
        ->assertSessionHasErrors('starts_on');
    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput(['kind' => 'exam']))->assertSessionHasErrors('exam_type');
});

it('asks before a change that moves RPM meetings (D-261)', function (): void {
    $calendar = copyCalendar($this->operator, $this->year);
    app()->bind(MeetingImpact::class, fn () => new class implements MeetingImpact
    {
        public function forDates(SchoolCalendar $calendar, array $dates, ?int $classId): CalendarImpact
        {
            return new CalendarImpact(12, 6);
        }
    });

    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput())
        ->assertSessionHasErrors(['confirm_impact' => '12 pertemuan RPM di 6 kelas akan bergeser.']);
    expect($calendar->events()->where('title', 'Karyawisata')->exists())->toBeFalse();

    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput(['confirmed' => true]))->assertSessionHasNoErrors();
    expect($calendar->events()->where('title', 'Karyawisata')->exists())->toBeTrue();
});

it('lets only the operator change the calendar; teachers and the principal propose (D-380)', function (): void {
    $subjectTeacher = User::factory()->teacherAt($this->school)->create();
    $principal = User::factory()->principalAt($this->school)->create();
    $calendar = copyCalendar($this->operator, $this->year);
    $events = $calendar->events()->count();

    actingAs($this->teacher)->get(route('calendar.show'))
        ->assertInertia(fn (Assert $page) => $page->where('can_manage', false)->where('can_propose', true)->where('selected_class', $this->class->public_id));

    // A wali kelas, a guru mapel, and the principal each send a proposal; nothing changes yet.
    actingAs($this->teacher)->post(route('calendar.events.store', $calendar), eventInput(['class' => $this->class->public_id, 'reason' => 'Karyawisata ke museum']))
        ->assertSessionHasNoErrors()->assertSessionHas('toast', 'Usulan dikirim ke operator sekolah.');
    $schoolEvent = $calendar->events()->whereNull('class_id')->orderBy('starts_on')->first();
    actingAs($subjectTeacher)->put(route('calendar.events.update', [$calendar, $schoolEvent]), eventInput(['title' => 'Hari pertama, MPLS', 'starts_on' => $schoolEvent->starts_on->toDateString(), 'ends_on' => $schoolEvent->starts_on->toDateString(), 'kind' => 'school_event']))
        ->assertSessionHasNoErrors();
    actingAs($principal)->delete(route('calendar.events.destroy', [$calendar, $schoolEvent]), ['reason' => 'Tidak diperlukan'])
        ->assertSessionHas('toast', 'Usulan dikirim ke operator sekolah.');

    expect($calendar->events()->count())->toBe($events)
        ->and(CalendarChangeRequest::withoutSchoolScope()->where('status', 'pending')->count())->toBe(3);

    // A proposal that breaks a rule is refused when it is sent.
    actingAs($this->teacher)->post(route('calendar.events.store', $calendar), eventInput(['kind' => 'exam', 'exam_type' => 'sas', 'starts_on' => '2026-12-25', 'ends_on' => '2026-12-25']))
        ->assertSessionHasErrors('starts_on');

    // Only the operator decides, and sees every pending proposal.
    $proposals = CalendarChangeRequest::withoutSchoolScope()->orderBy('id')->get();
    actingAs($this->teacher)->post(route('calendar.changes.approve', [$calendar, $proposals[0]]))->assertForbidden();
    actingAs($this->operator)->get(route('calendar.show'))->assertInertia(fn (Assert $page) => $page->has('change_requests', 3));

    actingAs($this->operator)->post(route('calendar.changes.approve', [$calendar, $proposals[0]]))->assertSessionHas('toast', 'Usulan disetujui dan kalender diperbarui.');
    actingAs($this->operator)->post(route('calendar.changes.approve', [$calendar, $proposals[1]]))->assertSessionHasNoErrors();
    actingAs($this->operator)->post(route('calendar.changes.reject', [$calendar, $proposals[2]]), ['note' => 'Tetap dipakai'])->assertSessionHas('toast', 'Usulan ditolak.');
    actingAs($this->operator)->post(route('calendar.changes.approve', [$calendar, $proposals[2]]))->assertSessionHasErrors('change');

    expect($calendar->events()->where('title', 'Karyawisata')->where('class_id', $this->class->id)->exists())->toBeTrue()
        ->and($schoolEvent->fresh()->title)->toBe('Hari pertama, MPLS')
        ->and(effective($this->class)['year']['effective_days'])->toBe(253)
        ->and(effective($this->other)['year']['effective_days'])->toBe(254);

    actingAs($principal)->get(route('calendar.show'))
        ->assertInertia(fn (Assert $page) => $page->has('change_requests', 1)->where('change_requests.0.status', 'rejected')->where('change_requests.0.decision_note', 'Tetap dipakai'));

    // The operator still edits directly.
    actingAs($this->operator)->post(route('calendar.events.store', $calendar), eventInput(['title' => 'Rapat orang tua', 'kind' => 'school_event']))
        ->assertSessionHas('toast', 'Kalender tersimpan.');
});

it('checks a proposal again when it is approved (D-380)', function (): void {
    $calendar = copyCalendar($this->operator, $this->year);

    actingAs($this->teacher)->post(route('calendar.events.store', $calendar), eventInput(['class' => $this->class->public_id]))->assertSessionHasNoErrors();
    $proposal = CalendarChangeRequest::withoutSchoolScope()->sole();
    $this->class->delete();

    actingAs($this->operator)->post(route('calendar.changes.approve', [$calendar, $proposal]))
        ->assertSessionHasErrors(['change' => 'Kelas tidak ditemukan di tahun ajaran ini.']);
    expect($proposal->fresh()->status->value)->toBe('pending');
});

it('keeps calendars and events of another school out of reach (IDOR)', function (): void {
    // Made before the first request: a request leaves its school in the context.
    $otherSchool = School::factory()->create(['region_id' => $this->school->region_id]);
    $otherOperator = User::factory()->operatorAt($otherSchool)->create();
    $otherYear = AcademicYear::factory()->for($otherSchool)->starting(2026)->create();
    $calendar = copyCalendar($this->operator, $this->year);
    $event = $calendar->events()->first();
    $otherCalendar = copyCalendar($otherOperator, $otherYear);

    actingAs($otherOperator)->post(route('calendar.events.store', $calendar), eventInput())->assertNotFound();
    actingAs($otherOperator)->delete(route('calendar.events.destroy', [$calendar, $event]))->assertNotFound();
    actingAs($otherOperator)->delete(route('calendar.events.destroy', [$otherCalendar, $event]))->assertNotFound();
    actingAs($otherOperator)->post(route('calendar.events.store', $otherCalendar), eventInput(['class' => $this->class->public_id]))->assertSessionHasErrors('class');
    actingAs($otherOperator)->get(route('calendar.show', ['tahun' => $this->year->public_id]))->assertNotFound();
    expect($event->fresh())->not->toBeNull();
});

it('adds provisional fixed-date holidays after the last SKB date (D-253)', function (): void {
    $days = app(NationalHolidays::class)->between(CarbonImmutable::parse('2027-12-01'), CarbonImmutable::parse('2028-06-30'));

    expect($days['2027-12-25']->isProvisional)->toBeFalse()
        ->and($days['2028-01-01']->isProvisional)->toBeTrue()
        ->and($days)->toHaveKeys(['2028-05-01', '2028-06-01'])
        ->and(array_keys($days))->not->toContain('2028-03-01');
});

it('adds up: effective = school days minus the three columns, semesters make the year', function (): void {
    mt_srand(7);

    foreach (range(1, 25) as $run) {
        $from = CarbonImmutable::parse('2026-07-01')->addDays(mt_rand(0, 20));
        $even = $from->addDays(mt_rand(150, 200));
        $to = $even->addDays(mt_rand(150, 180));
        $weekdays = array_values(array_unique([mt_rand(1, 2), 3, mt_rand(4, 6), 6]));
        sort($weekdays);
        $categories = [];

        foreach (range(1, 60) as $i) {
            $categories[$from->addDays(mt_rand(0, 360))->toDateString()] = DayCategory::cases()[mt_rand(0, 2)];
        }

        $table = EffectiveDays::compute($from, $even, $to, $weekdays, $categories)->toArray();
        $months = collect($table['months']);

        foreach ([...$table['months'], ...$table['semesters'], $table['year']] as $row) {
            expect($row['effective_days'])->toBe($row['school_days'] - $row['holidays'] - $row['assessments'] - $row['activities']);
        }

        expect($table['semesters'][1]['effective_days'] + $table['semesters'][2]['effective_days'])->toBe($table['year']['effective_days'])
            ->and($months->sum('effective_days'))->toBe($table['year']['effective_days'])
            ->and($months->where('semester', 1)->sum('school_days'))->toBe($table['semesters'][1]['school_days']);
    }
});

it('enforces the calendar constraints in the database', function (): void {
    $region = Region::query()->firstOrCreate(['province' => 'Jawa Tengah', 'regency' => 'Kabupaten Pati']);

    expect(fn () => DB::table('calendars')->insert(['public_id' => (string) str()->uuid(), 'region_id' => $region->id, 'school_id' => $this->school->id, 'academic_year' => '2030/2031', 'status' => 'active', 'version' => 1]))->toThrow(QueryException::class)
        ->and(fn () => DB::table('calendar_events')->insert(['public_id' => (string) str()->uuid(), 'calendar_id' => RegionCalendar::query()->sole()->id, 'starts_on' => '2026-08-01', 'ends_on' => '2026-08-01', 'kind' => 'exam', 'title' => 'x']))->toThrow(QueryException::class)
        ->and(fn () => DB::table('calendar_events')->insert(['public_id' => (string) str()->uuid(), 'calendar_id' => RegionCalendar::query()->sole()->id, 'starts_on' => '2026-08-02', 'ends_on' => '2026-08-01', 'kind' => 'holiday', 'title' => 'x']))->toThrow(QueryException::class)
        ->and(fn () => Region::query()->create(['province' => 'Jawa Tengah', 'regency' => null]))->toThrow(QueryException::class);
});
