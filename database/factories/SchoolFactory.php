<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SchoolLevel;
use App\Models\Region;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<School>
 */
final class SchoolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'SD Negeri '.fake()->unique()->numberBetween(1, 99999).' '.fake('id_ID')->city(),
            'npsn' => fake()->unique()->numerify('########'),
            'level' => SchoolLevel::Sd,
            'region_id' => Region::factory(),
            'address' => fake('id_ID')->address(),
        ];
    }

    public function madrasah(): static
    {
        return $this->state(fn (): array => [
            'name' => 'MI '.fake()->unique()->numberBetween(1, 99999).' '.fake('id_ID')->city(),
            'level' => SchoolLevel::Mi,
        ]);
    }
}
