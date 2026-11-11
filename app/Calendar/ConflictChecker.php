<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Actions\Timetable\TimetableClashes;
use App\Enums\CalendarEventKind;
use App\Enums\ExamType;
use App\Enums\NationalHolidayKind;
use App\Enums\TimetableSlotKind;
use App\Models\SchoolCalendar;
use App\Models\SchoolClass;
use Carbon\CarbonImmutable;

/**
 * One checker for every dated item, with the same messages everywhere (D-260). P5-03i
 * covers calendar events and the timetable; supervision schedules and Agenda & Rapat
 * add their rules in P5-09.
 *
 * Refused: an assessment (STS/SAS) on a national holiday or an active cuti bersama day.
 * Warning: an event on a Sunday, or on a day the class has no lessons.
 */
final readonly class ConflictChecker
{
    public function __construct(
        private NationalHolidays $holidays,
        private TimetableClashes $clashes,
    ) {}

    /**
     * @return list<Conflict>
     */
    public function forEvent(?SchoolCalendar $calendar, CalendarEventKind $kind, ?ExamType $examType, CarbonImmutable $startsOn, CarbonImmutable $endsOn, ?SchoolClass $class = null): array
    {
        $conflicts = [];

        if ($kind === CalendarEventKind::Exam && in_array($examType, [ExamType::Sts, ExamType::Sas], true)) {
            $schoolDays = $calendar->joint_leave_school_days ?? [];

            foreach ($this->holidays->between($startsOn, $endsOn) as $date => $holiday) {
                if ($holiday->kind === NationalHolidayKind::JointLeave && in_array($date, $schoolDays, true)) {
                    continue;
                }

                $conflicts[] = new Conflict(Conflict::ERROR, __('calendar.conflicts.exam_on_holiday', [
                    'exam' => $examType->label(),
                    'date' => self::date($date),
                    'holiday' => $holiday->name,
                ]));
            }
        }

        $list = [];

        for ($day = $startsOn; $day->lte($endsOn); $day = $day->addDay()) {
            $list[] = $day;
        }

        $days = collect($list);

        if ($days->contains(fn (CarbonImmutable $day): bool => $day->isSunday())) {
            $conflicts[] = new Conflict(Conflict::WARNING, __('calendar.conflicts.sunday'));
        }

        if ($class !== null) {
            $weekdays = $class->timetableSlots()->where('kind', TimetableSlotKind::Lesson->value)->distinct()->pluck('weekday')->map(fn ($day): int => (int) $day)->all();
            $off = $days->first(fn (CarbonImmutable $day): bool => ! $day->isSunday() && $weekdays !== [] && ! in_array($day->dayOfWeekIso, $weekdays, true));

            if ($off !== null) {
                $conflicts[] = new Conflict(Conflict::WARNING, __('calendar.conflicts.no_lessons_day', ['class' => $class->name, 'date' => self::date($off->toDateString())]));
            }
        }

        return $conflicts;
    }

    /**
     * A lesson row of the timetable: refused when the teacher is in another class at
     * that time (D-248), with the timetable's own message.
     *
     * @return list<Conflict>
     */
    public function forLesson(int $teacherId, string $teacherName, SchoolClass $class, int $weekday, string $startsAt, string $endsAt): array
    {
        $clash = $this->clashes->find($teacherId, $class, $weekday, $startsAt, $endsAt);

        return $clash === null ? [] : [new Conflict(Conflict::ERROR, $this->clashes->message($clash, $teacherName))];
    }

    public static function date(string $date): string
    {
        return CarbonImmutable::parse($date)->locale('id')->translatedFormat('l, j F Y');
    }
}
