<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Enums\NationalHolidayKind;
use App\Enums\TimetableSlotKind;
use App\Models\CalendarEvent;
use App\Models\SchoolCalendar;
use App\Models\SchoolClass;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

/**
 * Effective days and weeks per class (D-259): the days with a lesson in the class
 * timetable, minus national holidays, active cuti bersama (D-255), and the `holiday`,
 * `exam`, `school_event`, and `no_lessons` events of the school or the class (D-258).
 * One service, so every role reads the same numbers (P1).
 *
 * A class with no lessons on the timetable yet counts the region calendar's school days
 * per week (6 when unknown), and says so.
 */
final readonly class EffectiveDays
{
    public const int DEFAULT_DAYS_PER_WEEK = 6;

    public function __construct(private NationalHolidays $holidays) {}

    public function forClass(SchoolClass $class): EffectiveDaysTable
    {
        $year = $class->loadMissing('academicYear')->academicYear;
        $calendar = SchoolCalendar::query()->where('academic_year', $year->name)->with('sourceCalendar')->first();

        $weekdays = $class->timetableSlots()->where('kind', TimetableSlotKind::Lesson->value)->distinct()->orderBy('weekday')->pluck('weekday')->map(fn ($day): int => (int) $day)->all();
        $fromTimetable = $weekdays !== [];

        if (! $fromTimetable) {
            $weekdays = range(1, $calendar?->sourceCalendar->school_days_per_week ?? self::DEFAULT_DAYS_PER_WEEK);
        }

        return self::compute(
            $year->starts_on,
            $year->even_semester_starts_on,
            $year->ends_on,
            $weekdays,
            $this->categories($calendar, $class, $year->starts_on, $year->ends_on),
            $fromTimetable,
        );
    }

    /**
     * Why each date is not effective, for a class (or the whole school when `$class` is
     * null): national holidays and cuti bersama first, then the calendar's events.
     *
     * @return array<string, DayCategory> keyed by Y-m-d
     */
    public function categories(?SchoolCalendar $calendar, ?SchoolClass $class, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $schoolDays = $calendar->joint_leave_school_days ?? [];
        $categories = [];

        foreach ($this->holidays->between($from, $to) as $date => $holiday) {
            if ($holiday->kind === NationalHolidayKind::JointLeave && in_array($date, $schoolDays, true)) {
                continue;
            }

            $categories[$date] = DayCategory::Holiday;
        }

        if ($calendar === null) {
            return $categories;
        }

        $events = $calendar->events()
            ->where('starts_on', '<=', $to->toDateString())
            ->where('ends_on', '>=', $from->toDateString())
            ->where(fn ($query) => $query->whereNull('class_id')->when($class, fn ($query, SchoolClass $class) => $query->orWhere('class_id', $class->id)))
            ->get();

        foreach ($events as $event) {
            /** @var CalendarEvent $event */
            $category = DayCategory::forEvent($event->kind);

            foreach (CarbonPeriod::create($event->starts_on->max($from), $event->ends_on->min($to)) as $day) {
                $date = $day->toDateString();

                if (! isset($categories[$date]) || $category->rank() < $categories[$date]->rank()) {
                    $categories[$date] = $category;
                }
            }
        }

        return $categories;
    }

    /**
     * The table from plain inputs; kept pure for tests.
     *
     * @param  list<int>  $weekdays  ISO weekdays, 1 = Senin … 6 = Sabtu
     * @param  array<string, DayCategory>  $categories  keyed by Y-m-d
     */
    public static function compute(CarbonImmutable $from, CarbonImmutable $evenSemesterStartsOn, CarbonImmutable $to, array $weekdays, array $categories, bool $fromTimetable = true): EffectiveDaysTable
    {
        $perWeek = count($weekdays);
        $months = [];
        $monthSemesters = [];
        $semesters = [1 => new EffectiveDaysRow($perWeek), 2 => new EffectiveDaysRow($perWeek)];
        $year = new EffectiveDaysRow($perWeek);
        $dates = [];

        foreach (CarbonPeriod::create($from, $to) as $day) {
            $semester = $day->lt($evenSemesterStartsOn) ? 1 : 2;
            // A month that holds the semester start is split in two rows, so semester subtotals add up.
            $key = $day->format('Y-m').($day->isSameMonth($evenSemesterStartsOn) && $evenSemesterStartsOn->day > 1 ? "-s{$semester}" : '');
            $months[$key] ??= new EffectiveDaysRow($perWeek);
            $monthSemesters[$key] = $semester;

            if (! in_array($day->dayOfWeekIso, $weekdays, true)) {
                continue;
            }

            $category = $categories[$day->toDateString()] ?? null;

            foreach ([$months[$key], $semesters[$semester], $year] as $row) {
                $row->add($category);
            }

            if ($category === null) {
                $dates[] = $day->toDateString();
            }
        }

        return new EffectiveDaysTable($months, $monthSemesters, $semesters, $year, $weekdays, $fromTimetable, $dates);
    }
}
