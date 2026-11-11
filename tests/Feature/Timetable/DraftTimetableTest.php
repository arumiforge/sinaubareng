<?php

declare(strict_types=1);

use App\Actions\SchoolYear\ClassSubjects;
use App\Actions\Timetable\Draft\Cell;
use App\Actions\Timetable\Draft\ClassPlan;
use App\Actions\Timetable\Draft\DayFrame;
use App\Actions\Timetable\Draft\Demand;
use App\Actions\Timetable\Draft\DraftGenerator;
use App\Actions\Timetable\Draft\Preferences;
use App\Curriculum\Import\CurriculumImporter;
use App\Enums\TimetableSlotKind;
use App\Models\AcademicYear;
use App\Models\Region;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\TimetableDraft;
use App\Models\TimetableSlot;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\travelTo;

/**
 * @param  list<array{0: string, 1: int, 2: int}>  $subjects  code, jp, teacher id
 */
function plan(int $id, array $cells, array $subjects, array $religions = [], array $fixed = []): ClassPlan
{
    $demands = array_map(fn (array $s): Demand => new Demand($s[0], [['subject_id' => crc32($s[0]), 'code' => $s[0], 'name' => $s[0], 'teacher_id' => $s[2], 'teacher_name' => "T{$s[2]}"]], $s[1], $s[0] === 'PJOK', in_array($s[0], ['MTK', 'BIN'], true)), $subjects);

    if ($religions !== []) {
        $demands[] = new Demand('religion', array_map(fn (array $r): array => ['subject_id' => crc32($r[0]), 'code' => $r[0], 'name' => $r[0], 'teacher_id' => $r[1], 'teacher_name' => "T{$r[1]}"], $religions), 3);
    }

    return new ClassPlan($id, "c{$id}", "Kelas {$id}", $cells, $demands, $fixed, []);
}

function overlaps(array $a, array $b): bool
{
    return $a['weekday'] === $b['weekday'] && $a['starts_at'] < $b['ends_at'] && $a['ends_at'] > $b['starts_at'];
}

describe('the generator (pure)', function (): void {
    it('never breaks a hard rule and accounts for every JP (D-248…D-251, D-262)', function (): void {
        $frame = DayFrame::fromArray(config('timetable.day_frame'));
        mt_srand(11);

        foreach (range(1, 12) as $run) {
            $teachers = range(1, mt_rand(4, 9));
            $plans = [];
            $demanded = [];

            foreach (range(1, mt_rand(2, 4)) as $classId) {
                $subjects = [];
                foreach (['MTK' => 5, 'BIN' => 6, 'IPAS' => 5, 'PJOK' => 3, 'SB' => 3, 'PP' => 4, 'BJW' => 2] as $code => $jp) {
                    $subjects[] = [$code, mt_rand(max(1, $jp - 2), $jp + 2), $teachers[array_rand($teachers)]];
                }
                $religions = [['PAI', 100 + $classId], ['PAKR', 200]];
                $plans[] = plan($classId, $frame->cells(), $subjects, $religions);
                foreach ($subjects as [$code, $jp]) {
                    $demanded[$classId][$code] = $jp;
                }
                $demanded[$classId]['PAI'] = 3;
                $demanded[$classId]['PAKR'] = 3;
            }

            $busy = [1 => [[1, '07:00:00', '08:10:00']]];
            $seed = mt_rand(1, 1_000_000);
            $result = (new DraftGenerator)->generate($plans, $busy, Preferences::fromArray([]), $seed, 5);
            $teacherRows = [];

            foreach ($result['classes'] as $index => $class) {
                $classId = $index + 1;
                $lessons = array_values(array_filter($class['rows'], fn (array $row): bool => $row['kind'] === 'lesson'));
                $cellTimes = collect($frame->cells())->map(fn (Cell $cell): string => "{$cell->weekday} {$cell->startsAt}")->all();

                foreach ($lessons as $i => $row) {
                    expect($row['jp'])->toBeBetween(1, 4)
                        ->and(in_array("{$row['weekday']} {$row['starts_at']}", $cellTimes, true))->toBeTrue();

                    foreach (array_slice($lessons, $i + 1) as $other) {
                        $parallelReligions = in_array($row['subject'], ['PAI', 'PAKR'], true) && in_array($other['subject'], ['PAI', 'PAKR'], true) && $row['subject'] !== $other['subject'];
                        expect(overlaps($row, $other) && ! $parallelReligions)->toBeFalse();
                    }

                    $teacher = (int) substr((string) $row['teacher_name'], 1);
                    $teacherRows[$teacher][] = [...$row, 'class' => $classId];
                }

                $placed = collect($lessons)->groupBy('subject')->map(fn ($rows): int => $rows->sum('jp'));
                $missing = collect($class['unplaced'])->groupBy('subject')->map(fn ($rows): int => $rows->sum('jp'));

                foreach ($demanded[$classId] as $code => $jp) {
                    $religionMissing = $missing->get('PAI, PAKR', 0);
                    expect($placed->get($code, 0) + $missing->get($code, 0) + (in_array($code, ['PAI', 'PAKR'], true) ? $religionMissing : 0))->toBe($jp);
                }
            }

            foreach ($teacherRows as $teacher => $rows) {
                foreach ($rows as $i => $row) {
                    foreach (array_slice($rows, $i + 1) as $other) {
                        expect(overlaps($row, $other))->toBeFalse();
                    }
                    foreach ($busy[$teacher] ?? [] as [$weekday, $from, $to]) {
                        expect(overlaps($row, ['weekday' => $weekday, 'starts_at' => $from, 'ends_at' => $to]))->toBeFalse();
                    }
                }
            }

            expect((new DraftGenerator)->generate($plans, $busy, Preferences::fromArray([]), $seed, 5))->toBe($result);
        }
    });

    it('reports JP that do not fit, with the reason', function (): void {
        $cells = array_slice(DayFrame::fromArray(['days' => [1 => ['starts_at' => '07:00', 'periods' => 2]], 'breaks' => []])->cells(), 0, 2);
        $result = (new DraftGenerator)->generate([plan(1, $cells, [['MTK', 3, 1]])], [], Preferences::fromArray([]), 1, 3);

        expect($result['unplaced_jp'])->toBe(1)
            ->and($result['classes'][0]['unplaced'][0]['reason'])->toBe('Tidak ada jam kosong lagi di kerangka hari. Tambah JP di kerangka hari.');

        $busy = [1 => [[1, '07:00:00', '12:00:00']]];
        $result = (new DraftGenerator)->generate([plan(1, $cells, [['MTK', 1, 1]])], $busy, Preferences::fromArray([]), 1, 3);
        expect($result['classes'][0]['unplaced'][0]['reason'])->toBe('T1 sudah mengajar di kelas lain pada semua jam yang masih kosong.');
    });

    it('follows the soft switches when it can (D-262)', function (): void {
        $frame = DayFrame::fromArray(config('timetable.day_frame'));
        $result = (new DraftGenerator)->generate([plan(1, $frame->cells(), [['PJOK', 3, 1], ['MTK', 4, 2], ['IPAS', 4, 3]])], [], Preferences::fromArray([]), 5, 10);
        $rows = collect($result['classes'][0]['rows'])->where('kind', 'lesson');

        expect($result['score'])->toBe(0)
            ->and($rows->where('subject', 'PJOK')->every(fn (array $row): bool => $row['ends_at'] <= '10:00:00'))->toBeTrue()
            ->and($rows->groupBy('subject')->every(fn ($group): bool => $group->pluck('weekday')->duplicates()->isEmpty()))->toBeTrue()
            ->and($rows->where('subject', 'MTK')->every(fn (array $row): bool => $row['starts_at'] === '07:00:00'))->toBeTrue();
    });

    it('builds cells and breaks from the day frame', function (): void {
        $frame = DayFrame::fromArray(['days' => [1 => ['starts_at' => '07:00', 'periods' => 4], 2 => ['starts_at' => '07:30', 'periods' => 0]], 'breaks' => [['after_period' => 2, 'minutes' => 15, 'label' => 'Istirahat']]]);

        expect(array_map(fn (Cell $cell): string => "{$cell->startsAt}-{$cell->endsAt}/{$cell->segment}", $frame->cells()))
            ->toBe(['07:00:00-07:35:00/0', '07:35:00-08:10:00/0', '08:25:00-09:00:00/1', '09:00:00-09:35:00/1'])
            ->and($frame->breakRows())->toBe([['weekday' => 1, 'starts_at' => '08:10:00', 'ends_at' => '08:25:00', 'label' => 'Istirahat']]);
    });
});

describe('the Susun draf jadwal page', function (): void {
    beforeEach(function (): void {
        foreach (['docs/data/curriculum/subjects.json', 'docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json'] as $file) {
            app(CurriculumImporter::class)->import(base_path($file));
        }

        travelTo('2026-11-09');
        $this->school = School::factory()->create(['region_id' => Region::query()->where('regency', 'Kabupaten Kudus')->sole()->id]);
        $this->operator = User::factory()->operatorAt($this->school)->create();
        $this->year = AcademicYear::factory()->for($this->school)->starting(2026)->create();
        $this->class = SchoolClass::factory()->for($this->year)->create(['grade' => 4, 'name' => '4A']);
        $this->other = SchoolClass::factory()->for($this->year)->create(['grade' => 4, 'name' => '4B']);
        $this->teachers = User::factory()->count(4)->teacherAt($this->school)->create();

        // Every allocated subject has a teacher, except Pendidikan Agama other than Islam (not taught, D-251).
        foreach ([$this->class, $this->other] as $c => $class) {
            $offers = app(SchoolContext::class)->runFor($this->school->id, fn () => app(ClassSubjects::class)->handle($class));
            foreach ($offers->values() as $i => $offer) {
                if ($offer->jpPerWeek !== null && ($offer->subject->religion === null || $offer->subject->code === 'PAI')) {
                    TeachingAssignment::factory()->create(['class_id' => $class->id, 'subject_id' => $offer->subject->id, 'teacher_id' => $this->teachers[($i + $c) % 4]->id]);
                }
            }
        }
    });

    it('makes a draft on the queue, compares it, and saves it through the timetable rules (D-262)', function (): void {
        $mtk = Subject::query()->where('code', 'MTK')->sole();
        $locked = TimetableSlot::factory()->create(['class_id' => $this->class->id, 'weekday' => 1, 'starts_at' => '07:00', 'ends_at' => '07:35', 'kind' => TimetableSlotKind::Ceremony, 'is_locked' => true]);
        $loose = TimetableSlot::factory()->lesson($mtk, 1)->create(['class_id' => $this->class->id, 'weekday' => 2, 'starts_at' => '07:00', 'ends_at' => '07:35']);

        actingAs($this->operator)->post(route('timetable.draft.store'), ['tahun' => $this->year->public_id])
            ->assertRedirect(route('timetable.draft.show', ['tahun' => $this->year->public_id]));

        $draft = TimetableDraft::withoutSchoolScope()->sole();
        expect($draft->status)->toBe(TimetableDraft::READY)->and($draft->result['classes'])->toHaveCount(2);

        actingAs($this->operator)->get(route('timetable.draft.show'))
            ->assertInertia(fn (Assert $page) => $page->component('School/Timetable/Draft')->where('draft.status', 'ready')->has('current.'.$this->class->public_id, 2));

        actingAs($this->operator)->post(route('timetable.draft.save', $draft))->assertSessionHasNoErrors()->assertSessionHas('toast', 'Draf disimpan ke jadwal.');

        expect($locked->fresh())->not->toBeNull()
            ->and($loose->fresh())->toBeNull()
            ->and($draft->fresh()->status)->toBe(TimetableDraft::SAVED);

        foreach ([$this->class, $this->other] as $class) {
            $jp = TimetableSlot::withoutSchoolScope()->where('class_id', $class->id)->whereNotNull('subject_id')->sum('jp');
            $placed = collect($draft->result['classes'])->firstWhere('class', $class->public_id);
            $missing = collect($placed['unplaced'])->sum('jp');
            $allocated = app(SchoolContext::class)->runFor($this->school->id, fn () => app(ClassSubjects::class)->handle($class))
                ->filter(fn ($offer) => $offer->jpPerWeek !== null && ($offer->subject->religion === null || $offer->subject->code === 'PAI'))->sum('jpPerWeek');
            expect((int) $jp + $missing)->toBe($allocated);
        }

        actingAs($this->operator)->post(route('timetable.draft.save', $draft))->assertSessionHasErrors('draft');
    });

    it('gives the reason when a subject has no teacher', function (): void {
        TeachingAssignment::query()->where('class_id', $this->class->id)->where('subject_id', Subject::query()->where('code', 'SB')->sole()->id)->delete();

        actingAs($this->operator)->post(route('timetable.draft.store'), ['tahun' => $this->year->public_id, 'kelas' => $this->class->public_id]);

        $result = TimetableDraft::withoutSchoolScope()->sole()->result;
        expect($result['classes'])->toHaveCount(1)
            ->and(collect($result['classes'][0]['unplaced'])->firstWhere('subject', 'Seni dan Budaya')['reason'])->toBe('Belum ada guru. Pilih gurunya di halaman kelas.');
    });

    it('saves the day frame and switches per school', function (): void {
        actingAs($this->operator)->put(route('timetable.draft.settings'), [
            'frame' => ['days' => [1 => ['starts_at' => '07:15', 'periods' => 7], 6 => ['starts_at' => '07:00', 'periods' => 0]], 'breaks' => [['after_period' => 4, 'minutes' => 20, 'label' => 'Istirahat']]],
            'preferences' => ['pjok_before_10' => false],
        ])->assertSessionHasNoErrors();

        actingAs($this->operator)->get(route('timetable.draft.show'))
            ->assertInertia(fn (Assert $page) => $page->where('frame.days', ['1' => ['starts_at' => '07:15', 'periods' => 7]])->where('preferences.pjok_before_10', false)->where('preferences.spread_week', true));

        actingAs($this->operator)->put(route('timetable.draft.settings'), ['frame' => ['days' => [1 => ['starts_at' => '07:00', 'periods' => 20]], 'breaks' => []], 'preferences' => []])
            ->assertSessionHasErrors('frame.days.1.periods');
    });

    it('lets only the operator in, and keeps another school out (IDOR)', function (): void {
        actingAs($this->teachers[0])->get(route('timetable.draft.show'))->assertForbidden();
        actingAs($this->teachers[0])->post(route('timetable.draft.store'), ['tahun' => $this->year->public_id])->assertForbidden();

        actingAs($this->operator)->post(route('timetable.draft.store'), ['tahun' => $this->year->public_id]);
        $draft = TimetableDraft::withoutSchoolScope()->sole();

        $otherSchool = School::factory()->create(['region_id' => $this->school->region_id]);
        $otherOperator = User::factory()->operatorAt($otherSchool)->create();
        actingAs($otherOperator)->post(route('timetable.draft.save', $draft))->assertNotFound();
        actingAs($otherOperator)->post(route('timetable.draft.store'), ['tahun' => $this->year->public_id])->assertNotFound();
    });

    it('locks and unlocks a row on the class timetable', function (): void {
        $row = TimetableSlot::factory()->create(['class_id' => $this->class->id]);

        actingAs($this->operator)->put(route('classes.timetable.lock', [$this->class, $row]))->assertSessionHas('toast', 'Jam dikunci. Draf jadwal tidak akan mengubahnya.');
        expect($row->fresh()->is_locked)->toBeTrue();
        actingAs($this->teachers[0])->put(route('classes.timetable.lock', [$this->class, $row]))->assertForbidden();
    });
});
