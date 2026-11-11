<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Models\AcademicYear;
use App\Models\CalendarEvent;
use App\Models\RegionCalendar;
use App\Models\School;
use App\Models\SchoolCalendar;
use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * "Salin dari kalender wilayah" and "Terapkan perubahan wilayah" (D-256): a school copies
 * its region's reference calendar once, then sees each later region change as an offer
 * to apply or skip. Copied events remember the region event they came from.
 */
final class RegionCalendarSync
{
    /** The active region calendar the school's region uses for the year, if any (D-323). */
    public function regionCalendarFor(School $school, AcademicYear $year): ?RegionCalendar
    {
        return RegionCalendar::forRegion($school->region, $year->name);
    }

    /** Copies the region calendar; the year must have no school calendar yet. */
    public function copy(RegionCalendar $region, AcademicYear $year): SchoolCalendar
    {
        return DB::transaction(function () use ($region, $year): SchoolCalendar {
            if (SchoolCalendar::query()->where('academic_year', $year->name)->lockForUpdate()->exists()) {
                throw new LogicException('The year already has a school calendar.');
            }

            $calendar = SchoolCalendar::query()->create([
                'academic_year' => $year->name,
                'source_calendar_id' => $region->id,
                'source_version' => $region->version,
                'joint_leave_school_days' => $region->joint_leave_school_days ?? [],
            ]);

            foreach ($region->events()->orderBy('starts_on')->get() as $event) {
                $this->copyEvent($calendar, $event);
            }

            return $calendar;
        });
    }

    /** A calendar with no region source, for a school whose region has none yet. */
    public function createEmpty(AcademicYear $year): SchoolCalendar
    {
        return SchoolCalendar::query()->firstOrCreate(['academic_year' => $year->name], ['joint_leave_school_days' => []]);
    }

    /**
     * What differs from the region calendar. Empty when the school has no source.
     *
     * @return list<RegionChange>
     */
    public function changes(SchoolCalendar $calendar): array
    {
        $region = $calendar->sourceCalendar;

        if ($region === null) {
            return [];
        }

        $regionEvents = $region->events()->orderBy('starts_on')->get()->keyBy('public_id');
        $copied = $calendar->events()->whereNotNull('source_event_uid')->get()->keyBy('source_event_uid');
        $changes = [];

        foreach ($regionEvents as $uid => $event) {
            if (! $copied->has($uid)) {
                $changes[] = new RegionChange(RegionChange::ADDED, $event);
            }
        }

        foreach ($copied as $uid => $event) {
            if (! $regionEvents->has($uid)) {
                $changes[] = new RegionChange(RegionChange::REMOVED, $event);
            }
        }

        $regionDays = $region->joint_leave_school_days ?? [];
        $schoolDays = $calendar->joint_leave_school_days ?? [];

        foreach (array_diff($regionDays, $schoolDays) as $date) {
            $changes[] = new RegionChange(RegionChange::SCHOOL_DAY_ADDED, date: $date);
        }

        foreach (array_diff($schoolDays, $regionDays) as $date) {
            $changes[] = new RegionChange(RegionChange::SCHOOL_DAY_REMOVED, date: $date);
        }

        return $changes;
    }

    /** Applies every offered change and marks the region version as seen. */
    public function apply(SchoolCalendar $calendar): void
    {
        DB::transaction(function () use ($calendar): void {
            $days = $calendar->joint_leave_school_days ?? [];

            foreach ($this->changes($calendar) as $change) {
                match ($change->type) {
                    RegionChange::ADDED => $this->copyEvent($calendar, $change->event),
                    RegionChange::REMOVED => $change->event?->delete(),
                    RegionChange::SCHOOL_DAY_ADDED => $days[] = (string) $change->date,
                    RegionChange::SCHOOL_DAY_REMOVED => $days = array_values(array_diff($days, [(string) $change->date])),
                    default => null,
                };
            }

            sort($days);
            $calendar->forceFill(['joint_leave_school_days' => array_values(array_unique($days))]);
            $this->markSeen($calendar);
        });
    }

    /** Skips the offered changes until the region calendar changes again. */
    public function skip(SchoolCalendar $calendar): void
    {
        $this->markSeen($calendar);
    }

    private function markSeen(SchoolCalendar $calendar): void
    {
        $calendar->forceFill(['source_version' => $calendar->sourceCalendar?->version])->save();
    }

    private function copyEvent(SchoolCalendar $calendar, ?CalendarEvent $event): void
    {
        if ($event === null) {
            return;
        }

        $calendar->events()->create([
            'kind' => $event->kind,
            'exam_type' => $event->exam_type,
            'title' => $event->title,
            'starts_on' => $event->starts_on,
            'ends_on' => $event->ends_on,
            'source_event_uid' => $event->public_id,
        ]);
    }
}
