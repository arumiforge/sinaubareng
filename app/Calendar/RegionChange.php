<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Models\CalendarEvent;

/**
 * One difference between a school calendar and the region calendar it was copied from
 * (D-256): an event the region added or removed, or a cuti bersama day the region now
 * keeps as a school day (or no longer does).
 */
final readonly class RegionChange
{
    public const string ADDED = 'added';

    public const string REMOVED = 'removed';

    public const string SCHOOL_DAY_ADDED = 'school_day_added';

    public const string SCHOOL_DAY_REMOVED = 'school_day_removed';

    public function __construct(
        public string $type,
        public ?CalendarEvent $event = null,
        public ?string $date = null,
    ) {}

    /** @return list<string> the dates whose status the change touches */
    public function dates(): array
    {
        if ($this->event === null) {
            return [(string) $this->date];
        }

        $dates = [];

        for ($day = $this->event->starts_on; $day->lte($this->event->ends_on); $day = $day->addDay()) {
            $dates[] = $day->toDateString();
        }

        return $dates;
    }

    /**
     * @return array{type: string, message: string}
     */
    public function toArray(): array
    {
        $event = $this->event;
        $when = $event === null ? ConflictChecker::date((string) $this->date) : self::range($event);

        return [
            'type' => $this->type,
            'message' => __("calendar.region_changes.{$this->type}", [
                'title' => $event?->title,
                'kind' => $event?->kind->label(),
                'when' => $when,
            ]),
        ];
    }

    private static function range(CalendarEvent $event): string
    {
        $from = ConflictChecker::date($event->starts_on->toDateString());

        return $event->starts_on->eq($event->ends_on) ? $from : $from.' – '.ConflictChecker::date($event->ends_on->toDateString());
    }
}
