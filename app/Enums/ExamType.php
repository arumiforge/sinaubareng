<?php

declare(strict_types=1);

namespace App\Enums;

/** The assessment an `exam` calendar event holds: STS, SAS, or another one (D-260, D-269). */
enum ExamType: string
{
    case Sts = 'sts';
    case Sas = 'sas';
    case Other = 'other';

    public function label(): string
    {
        return __("enums.exam_type.{$this->value}");
    }
}
