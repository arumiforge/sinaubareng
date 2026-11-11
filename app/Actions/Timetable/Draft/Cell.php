<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

/** One JP of the day frame: a weekday, its place in the day, times, and the part of the day between breaks. */
final readonly class Cell
{
    public function __construct(
        public int $weekday,
        public int $index,
        public string $startsAt,
        public string $endsAt,
        public int $segment,
    ) {}

    public function key(): string
    {
        return "{$this->weekday}-{$this->index}";
    }

    public function overlaps(string $startsAt, string $endsAt): bool
    {
        return $this->startsAt < $endsAt && $this->endsAt > $startsAt;
    }
}
