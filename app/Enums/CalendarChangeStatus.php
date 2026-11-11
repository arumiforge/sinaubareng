<?php

declare(strict_types=1);

namespace App\Enums;

/** A proposed calendar change waits for the operator, who approves or rejects it (D-380). */
enum CalendarChangeStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return __("enums.calendar_change_status.{$this->value}");
    }
}
