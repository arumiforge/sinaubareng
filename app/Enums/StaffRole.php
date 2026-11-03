<?php

declare(strict_types=1);

namespace App\Enums;

enum StaffRole: string
{
    case Admin = 'admin';
    case Support = 'support';
    case Content = 'content';

    public function label(): string
    {
        return __("enums.staff_role.{$this->value}");
    }
}
