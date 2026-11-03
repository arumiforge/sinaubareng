<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Region>
 */
final class RegionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'province' => fake()->randomElement(['Jawa Tengah', 'Jawa Timur', 'Jawa Barat', 'DI Yogyakarta', 'Banten']),
            'regency' => fake('id_ID')->unique()->city(),
        ];
    }
}
