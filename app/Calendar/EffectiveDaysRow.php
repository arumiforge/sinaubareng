<?php

declare(strict_types=1);

namespace App\Calendar;

/** One row of the effective-days table: a month, a semester, or the year (D-259). */
final class EffectiveDaysRow
{
    public int $schoolDays = 0;

    public int $holidays = 0;

    public int $assessments = 0;

    public int $activities = 0;

    public function __construct(public readonly int $daysPerWeek) {}

    public function effectiveDays(): int
    {
        return $this->schoolDays - $this->holidays - $this->assessments - $this->activities;
    }

    /** Stored and passed on in full; shown to one decimal (D-259). */
    public function effectiveWeeks(): float
    {
        return $this->daysPerWeek === 0 ? 0.0 : $this->effectiveDays() / $this->daysPerWeek;
    }

    public function add(?DayCategory $category): void
    {
        $this->schoolDays++;

        match ($category) {
            DayCategory::Holiday => $this->holidays++,
            DayCategory::Assessment => $this->assessments++,
            DayCategory::Activity => $this->activities++,
            null => null,
        };
    }

    /**
     * @return array{school_days: int, holidays: int, assessments: int, activities: int, effective_days: int, effective_weeks: float}
     */
    public function toArray(): array
    {
        return [
            'school_days' => $this->schoolDays,
            'holidays' => $this->holidays,
            'assessments' => $this->assessments,
            'activities' => $this->activities,
            'effective_days' => $this->effectiveDays(),
            'effective_weeks' => $this->effectiveWeeks(),
        ];
    }
}
