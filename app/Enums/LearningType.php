<?php

declare(strict_types=1);

namespace App\Enums;

enum LearningType: string
{
    case Intracurricular = 'intracurricular';
    case Cocurricular = 'cocurricular';

    public function label(): string
    {
        return __("enums.learning_type.{$this->value}");
    }
}
