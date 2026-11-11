<?php

declare(strict_types=1);

namespace App\Calendar;

/**
 * Effective days per month, per semester, and for the year (D-259), plus the dates
 * themselves for scheduling (P5-04).
 */
final readonly class EffectiveDaysTable
{
    /**
     * @param  array<string, EffectiveDaysRow>  $months  keyed by Y-m
     * @param  array<string, int>  $monthSemesters  Y-m => 1|2 (a month split by the semester start appears as two keys, see EffectiveDays)
     * @param  array{1: EffectiveDaysRow, 2: EffectiveDaysRow}  $semesters
     * @param  list<int>  $weekdays  ISO weekdays with lessons, 1 = Senin
     * @param  list<string>  $effectiveDates  Y-m-d
     */
    public function __construct(
        public array $months,
        public array $monthSemesters,
        public array $semesters,
        public EffectiveDaysRow $year,
        public array $weekdays,
        public bool $fromTimetable,
        public array $effectiveDates,
    ) {}

    /**
     * @return array{months: list<array<string, mixed>>, semesters: array<int, array<string, mixed>>, year: array<string, mixed>, weekdays: list<int>, days_per_week: int, from_timetable: bool}
     */
    public function toArray(): array
    {
        $months = [];

        foreach ($this->months as $key => $row) {
            $month = substr($key, 0, 7);
            $months[] = ['key' => $key, 'month' => $month, 'semester' => $this->monthSemesters[$key], ...$row->toArray()];
        }

        return [
            'months' => $months,
            'semesters' => [1 => $this->semesters[1]->toArray(), 2 => $this->semesters[2]->toArray()],
            'year' => $this->year->toArray(),
            'weekdays' => $this->weekdays,
            'days_per_week' => count($this->weekdays),
            'from_timetable' => $this->fromTimetable,
        ];
    }
}
