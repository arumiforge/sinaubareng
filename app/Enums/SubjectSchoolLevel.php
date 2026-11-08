<?php

declare(strict_types=1);

namespace App\Enums;

enum SubjectSchoolLevel: string
{
    case Sd = 'sd';
    case Mi = 'mi';
    case Both = 'both';

    public function label(): string
    {
        return __("enums.subject_school_level.{$this->value}");
    }
}
