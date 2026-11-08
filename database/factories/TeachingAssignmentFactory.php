<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeachingAssignment>
 */
final class TeachingAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'class_id' => SchoolClass::factory(),
            'school_id' => fn (array $attributes): int => SchoolClass::withoutSchoolScope()->findOrFail($attributes['class_id'])->school_id,
            'subject_id' => Subject::factory(),
            'teacher_id' => User::factory(),
        ];
    }
}
