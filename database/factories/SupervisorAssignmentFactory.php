<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\School;
use App\Models\SupervisorAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupervisorAssignment>
 */
final class SupervisorAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'user_id' => User::factory(),
            'started_on' => now()->subYear()->toDateString(),
            'ended_on' => null,
        ];
    }
}
