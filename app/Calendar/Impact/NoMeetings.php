<?php

declare(strict_types=1);

namespace App\Calendar\Impact;

use App\Models\SchoolCalendar;

/** The counter until RPM meetings exist (P5-04): no meetings, no impact. */
final class NoMeetings implements MeetingImpact
{
    public function forDates(SchoolCalendar $calendar, array $dates, ?int $classId): CalendarImpact
    {
        return new CalendarImpact(0, 0);
    }
}
