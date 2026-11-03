<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MembershipRole;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function principalAt(School $school): static
    {
        return $this->memberAt($school, MembershipRole::Principal);
    }

    public function operatorAt(School $school): static
    {
        return $this->memberAt($school, MembershipRole::Operator);
    }

    public function teacherAt(School $school): static
    {
        return $this->memberAt($school, MembershipRole::Teacher);
    }

    public function memberAt(School $school, MembershipRole $role): static
    {
        return $this->afterCreating(function (User $user) use ($school, $role): void {
            SchoolMembership::factory()->for($school)->for($user)->role($role)->create();
        });
    }
}
