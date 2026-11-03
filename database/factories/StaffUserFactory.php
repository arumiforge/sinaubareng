<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\StaffRole;
use App\Models\StaffUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<StaffUser>
 */
final class StaffUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => StaffRole::Admin,
            'remember_token' => Str::random(10),
        ];
    }

    public function role(StaffRole $role): static
    {
        return $this->state(fn (): array => ['role' => $role]);
    }
}
