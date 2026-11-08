<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DefaultTeacher;
use App\Enums\SubjectKind;
use App\Enums\SubjectSchoolLevel;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
final class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'name' => fake()->unique()->words(2, true),
            'kind' => SubjectKind::Compulsory,
            'default_teacher' => DefaultTeacher::ClassTeacher,
            'religion' => null,
            'school_level' => SubjectSchoolLevel::Both,
            'aliases' => [],
        ];
    }

    public function localContent(): self
    {
        return $this->state(['kind' => SubjectKind::LocalContent, 'default_teacher' => DefaultTeacher::SubjectTeacher]);
    }
}
