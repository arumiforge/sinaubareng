<?php

declare(strict_types=1);

namespace App\Enums;

enum DefaultTeacher: string
{
    case ClassTeacher = 'class_teacher';
    case SubjectTeacher = 'subject_teacher';

    public function label(): string
    {
        return __("enums.default_teacher.{$this->value}");
    }
}
