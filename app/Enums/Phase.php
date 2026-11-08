<?php

declare(strict_types=1);

namespace App\Enums;

enum Phase: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';

    public function label(): string
    {
        return __("enums.phase.{$this->value}");
    }
}
