<?php

declare(strict_types=1);

namespace App\Calendar\Impact;

/** What a calendar change would move: RPM meetings in how many classes (D-261). */
final readonly class CalendarImpact
{
    public function __construct(public int $meetings, public int $classes) {}

    public function isEmpty(): bool
    {
        return $this->meetings === 0;
    }

    public function message(): string
    {
        return __('calendar.impact.message', ['meetings' => $this->meetings, 'classes' => $this->classes]);
    }
}
