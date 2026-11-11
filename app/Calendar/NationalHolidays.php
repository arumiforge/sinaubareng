<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Enums\NationalHolidayKind;
use App\Models\NationalHoliday;
use Carbon\CarbonImmutable;

/**
 * National holidays and cuti bersama for a date range, merged into every calendar view
 * even when a school has no calendar yet (D-253). Dates after the last imported SKB date
 * get the fixed-date holidays, marked "sementara" until the SKB is out.
 */
final class NationalHolidays
{
    /** Month-day => name of the holidays with a fixed date (D-253). */
    public const array FIXED = [
        '01-01' => 'Tahun Baru Masehi',
        '05-01' => 'Hari Buruh Internasional',
        '06-01' => 'Hari Lahir Pancasila',
        '08-17' => 'Hari Proklamasi Kemerdekaan RI',
        '12-25' => 'Hari Raya Natal',
    ];

    /**
     * @return array<string, HolidayDay> keyed by Y-m-d, in date order
     */
    public function between(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $days = [];

        foreach (NationalHoliday::query()->whereBetween('date', [$from->toDateString(), $to->toDateString()])->orderBy('date')->get() as $row) {
            $date = $row->date->toDateString();
            $days[$date] = new HolidayDay($date, $row->name, $row->kind, $row->is_provisional);
        }

        $coveredUntil = NationalHoliday::query()->max('date');
        $start = $coveredUntil === null ? $from : CarbonImmutable::parse((string) $coveredUntil)->addDay()->max($from);

        for ($year = $start->year; $year <= $to->year; $year++) {
            foreach (self::FIXED as $monthDay => $name) {
                $date = CarbonImmutable::parse("{$year}-{$monthDay}");

                // Explicit bounds: Carbon's between() swaps a reversed range, which would add dates past `$to`.
                if ($date->gte($start) && $date->lte($to) && ! isset($days[$date->toDateString()])) {
                    $days[$date->toDateString()] = new HolidayDay($date->toDateString(), $name, NationalHolidayKind::NationalHoliday, true);
                }
            }
        }

        ksort($days);

        return $days;
    }
}
