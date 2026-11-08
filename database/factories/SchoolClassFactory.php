<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolClass>
 */
final class SchoolClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'school_id' => fn (array $attributes): int => AcademicYear::withoutSchoolScope()->findOrFail($attributes['academic_year_id'])->school_id,
            'grade' => 4,
            'name' => '4'.fake()->unique()->randomLetter(),
            'homeroom_teacher_id' => null,
        ];
    }
}
