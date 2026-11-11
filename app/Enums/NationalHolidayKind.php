<?php

declare(strict_types=1);

namespace App\Enums;

/** A national holiday or a cuti bersama day from the SKB (D-253). */
enum NationalHolidayKind: string
{
    case NationalHoliday = 'national_holiday';
    case JointLeave = 'joint_leave';

    public function label(): string
    {
        return __("enums.national_holiday_kind.{$this->value}");
    }
}
