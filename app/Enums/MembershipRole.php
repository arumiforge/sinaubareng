<?php

declare(strict_types=1);

namespace App\Enums;

enum MembershipRole: string
{
    case Principal = 'principal';
    case Operator = 'operator';
    case Teacher = 'teacher';

    public function label(): string
    {
        return __("enums.membership_role.{$this->value}");
    }
}
