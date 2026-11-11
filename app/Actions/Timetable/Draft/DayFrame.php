<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

use App\Models\TimetableSlot;
use Carbon\CarbonImmutable;

/**
 * The operator's day frame (D-262): per weekday a start time and a number of JP, and
 * breaks after given JP that hold for every day. Cells are 1 JP each (35 minutes).
 */
final readonly class DayFrame
{
    /**
     * @param  array<int, array{starts_at: string, periods: int}>  $days
     * @param  list<array{after_period: int, minutes: int, label: string|null}>  $breaks
     */
    public function __construct(public array $days, public array $breaks) {}

    /**
     * @param  array{days?: array<int|string, array{starts_at: string, periods: int|string}>, breaks?: list<array{after_period: int|string, minutes: int|string, label?: string|null}>}  $data
     */
    public static function fromArray(array $data): self
    {
        $days = [];

        foreach ($data['days'] ?? [] as $weekday => $day) {
            if ((int) $day['periods'] > 0) {
                $days[(int) $weekday] = ['starts_at' => substr((string) $day['starts_at'], 0, 5), 'periods' => (int) $day['periods']];
            }
        }

        ksort($days);
        $breaks = array_map(fn (array $break): array => [
            'after_period' => (int) $break['after_period'],
            'minutes' => (int) $break['minutes'],
            'label' => ($break['label'] ?? null) ?: null,
        ], $data['breaks'] ?? []);
        usort($breaks, fn (array $a, array $b): int => $a['after_period'] <=> $b['after_period']);

        return new self($days, $breaks);
    }

    /**
     * @return array{days: array<int, array{starts_at: string, periods: int}>, breaks: list<array{after_period: int, minutes: int, label: string|null}>}
     */
    public function toArray(): array
    {
        return ['days' => $this->days, 'breaks' => $this->breaks];
    }

    /**
     * @return list<Cell>
     */
    public function cells(): array
    {
        $cells = [];

        foreach ($this->days as $weekday => $day) {
            $time = CarbonImmutable::parse($day['starts_at']);
            $segment = 0;

            for ($period = 1; $period <= $day['periods']; $period++) {
                $end = $time->addMinutes(TimetableSlot::MINUTES_PER_JP);
                $cells[] = new Cell($weekday, $period, $time->format('H:i:s'), $end->format('H:i:s'), $segment);
                $time = $end;

                foreach ($this->breaks as $break) {
                    if ($break['after_period'] === $period && $period < $day['periods']) {
                        $time = $time->addMinutes($break['minutes']);
                        $segment++;
                    }
                }
            }
        }

        return $cells;
    }

    /**
     * The break rows of each day, between the JP of the frame.
     *
     * @return list<array{weekday: int, starts_at: string, ends_at: string, label: string|null}>
     */
    public function breakRows(): array
    {
        $rows = [];
        $cells = collect($this->cells())->groupBy('weekday');

        foreach ($cells as $weekday => $day) {
            foreach ($day->values() as $i => $cell) {
                $next = $day->values()->get($i + 1);

                if ($next !== null && $next->segment !== $cell->segment && $next->startsAt > $cell->endsAt) {
                    $label = collect($this->breaks)->firstWhere('after_period', $cell->index)['label'] ?? null;
                    $rows[] = ['weekday' => (int) $weekday, 'starts_at' => $cell->endsAt, 'ends_at' => $next->startsAt, 'label' => $label];
                }
            }
        }

        return $rows;
    }
}
