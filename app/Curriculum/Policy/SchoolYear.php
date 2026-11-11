<?php

declare(strict_types=1);

namespace App\Curriculum\Policy;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

/** School-year names like `2026/2027`, compared by their first year (D-354). */
final class SchoolYear
{
    /** The school year a date falls in; a year starts in July. */
    public static function forDate(CarbonImmutable $date): string
    {
        $start = $date->month >= 7 ? $date->year : $date->year - 1;

        return $start.'/'.($start + 1);
    }

    public static function startYear(string $schoolYear): int
    {
        if (preg_match('/^(\d{4})\/(\d{4})$/', $schoolYear, $match) !== 1 || (int) $match[2] !== (int) $match[1] + 1) {
            throw new InvalidArgumentException("'{$schoolYear}' is not a school year like 2026/2027.");
        }

        return (int) $match[1];
    }

    /** Whether `$from` (NULL = always) has started by `$schoolYear`. */
    public static function hasStarted(?string $from, string $schoolYear): bool
    {
        return $from === null || self::startYear($from) <= self::startYear($schoolYear);
    }
}
