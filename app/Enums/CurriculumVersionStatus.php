<?php

declare(strict_types=1);

namespace App\Enums;

enum CurriculumVersionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Retired = 'retired';

    public function label(): string
    {
        return __("enums.curriculum_version_status.{$this->value}");
    }
}
