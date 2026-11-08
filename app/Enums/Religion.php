<?php

declare(strict_types=1);

namespace App\Enums;

enum Religion: string
{
    case Islam = 'islam';
    case Kristen = 'kristen';
    case Katolik = 'katolik';
    case Hindu = 'hindu';
    case Buddha = 'buddha';
    case Khonghucu = 'khonghucu';

    public function label(): string
    {
        return __("enums.religion.{$this->value}");
    }
}
