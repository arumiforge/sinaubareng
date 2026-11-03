<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolMembership>
 */
final class SchoolMembershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'user_id' => User::factory(),
            'role' => MembershipRole::Teacher,
            'status' => MembershipStatus::Active,
            'started_on' => now()->subYear()->toDateString(),
            'ended_on' => null,
        ];
    }

    public function role(MembershipRole $role): static
    {
        return $this->state(fn (): array => ['role' => $role]);
    }

    public function ended(): static
    {
        return $this->state(fn (): array => [
            'status' => MembershipStatus::Ended,
            'ended_on' => now()->toDateString(),
        ]);
    }
}
