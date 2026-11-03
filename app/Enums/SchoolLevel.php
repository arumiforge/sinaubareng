<?php

declare(strict_types=1);

namespace App\Enums;

enum SchoolLevel: string
{
    case Sd = 'sd';
    case Mi = 'mi';

    public function label(): string
    {
        return __("enums.school_level.{$this->value}");
    }
}
