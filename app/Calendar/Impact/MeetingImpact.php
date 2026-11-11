<?php

declare(strict_types=1);

namespace App\Calendar\Impact;

use App\Models\SchoolCalendar;

/**
 * Counts the RPM meetings a calendar change would move (D-261). P5-04 brings `meetings`
 * and binds its own counter; until then there are no meetings, so nothing is shown.
 */
interface MeetingImpact
{
    /**
     * @param  list<string>  $dates  Y-m-d dates whose status changes
     * @param  int|null  $classId  the class the change is limited to, or NULL for the school
     */
    public function forDates(SchoolCalendar $calendar, array $dates, ?int $classId): CalendarImpact;
}
