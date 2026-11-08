<?php

declare(strict_types=1);

namespace App\Enums;

enum SubjectKind: string
{
    case Compulsory = 'compulsory';
    case LocalContent = 'local_content';
    case Cocurricular = 'cocurricular';

    public function label(): string
    {
        return __("enums.subject_kind.{$this->value}");
    }
}
