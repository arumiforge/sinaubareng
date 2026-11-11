<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

/** The soft preference switches of a school (D-262). Hard rules are never switches. */
final readonly class Preferences
{
    public const array KEYS = ['max_consecutive', 'pjok_before_10', 'core_early', 'spread_week', 'no_split_break'];

    /**
     * @param  array<string, bool>  $switches
     */
    public function __construct(public array $switches) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        /** @var array<string, bool> $defaults */
        $defaults = config('timetable.preferences');
        $switches = [];

        foreach (self::KEYS as $key) {
            $switches[$key] = (bool) ($data[$key] ?? $defaults[$key] ?? true);
        }

        return new self($switches);
    }

    public function on(string $key): bool
    {
        return $this->switches[$key] ?? false;
    }
}
