<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MembershipRole;
use App\Models\Invitation;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invitation>
 */
final class InvitationFactory extends Factory
{
    /** Plain token of the last invitation made with withToken(). */
    public const TOKEN = 'token-undangan-untuk-tes-0123456789abcdef';

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'email' => fake()->unique()->safeEmail(),
            'role' => MembershipRole::Teacher,
            'token_hash' => Invitation::hashToken(Str::random(40)),
            'expires_at' => now()->addDays(Invitation::VALID_DAYS),
            'accepted_at' => null,
            'invited_by' => null,
            'invited_by_staff_id' => null,
        ];
    }

    public function role(MembershipRole $role): static
    {
        return $this->state(fn (): array => ['role' => $role]);
    }

    /**
     * Uses a known token, so a test can open the mailed link.
     */
    public function withToken(string $token = self::TOKEN): static
    {
        return $this->state(fn (): array => ['token_hash' => Invitation::hashToken($token)]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => ['expires_at' => now()->subMinute()]);
    }

    public function accepted(): static
    {
        return $this->state(fn (): array => ['accepted_at' => now()->subDay()]);
    }
}
