<?php

declare(strict_types=1);

namespace App\Enums;

/** A region reference calendar is `draft` until Tim Validasi SIGRAK signs it off (D-113, D-256). */
enum CalendarStatus: string
{
    case Draft = 'draft';
    case Active = 'active';

    public function label(): string
    {
        return __("enums.calendar_status.{$this->value}");
    }
}
