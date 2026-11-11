<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NationalHolidayKind;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * A national holiday or cuti bersama day (D-253). Global and read-only for schools;
 * Tim SIGRAK keeps it in the console or with `calendar:import-holidays`.
 *
 * @property int $id
 * @property CarbonImmutable $date
 * @property string $name
 * @property NationalHolidayKind $kind
 * @property string|null $decree
 * @property string $academic_year
 * @property bool $is_provisional
 */
#[Fillable(['date', 'name', 'kind', 'decree', 'academic_year', 'is_provisional'])]
final class NationalHoliday extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'immutable_date',
            'kind' => NationalHolidayKind::class,
            'is_provisional' => 'boolean',
        ];
    }
}
