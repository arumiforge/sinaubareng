<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\School;
use App\Models\SchoolSubject;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolSubject>
 */
final class SchoolSubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'subject_id' => Subject::factory()->localContent(),
            'grade' => null,
            'enabled' => true,
            'in_report' => true,
        ];
    }
}
