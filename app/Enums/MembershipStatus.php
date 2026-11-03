<?php

declare(strict_types=1);

namespace App\Enums;

enum MembershipStatus: string
{
    case Active = 'active';
    case Ended = 'ended';

    public function label(): string
    {
        return __("enums.membership_status.{$this->value}");
    }
}
