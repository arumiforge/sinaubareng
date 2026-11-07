<?php

declare(strict_types=1);

namespace App\Tenancy\Actions;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Invitation;
use App\Models\SchoolMembership;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Turns an open invitation into a membership. A new person gets an account with the
 * password they chose; someone who already has an account confirms with their password.
 */
final class AcceptInvitation
{
    public function __construct(private readonly SchoolContext $context) {}

    /**
     * @param  array{name?: string|null, password: string}  $input
     */
    public function handle(Invitation $invitation, array $input): SchoolMembership
    {
        return $this->context->runFor($invitation->school_id, fn (): SchoolMembership => DB::transaction(function () use ($invitation, $input): SchoolMembership {
            // Lock the row, so a double submit cannot accept twice.
            $invitation = Invitation::query()->lockForUpdate()->findOrFail($invitation->id);

            if (! $invitation->isPending()) {
                throw ValidationException::withMessages(['password' => __('auth.invitation.unavailable')]);
            }

            $user = $this->user($invitation, $input);
            $membership = $this->membership($invitation, $user);

            $invitation->forceFill(['accepted_at' => now()])->save();

            return $membership;
        }));
    }

    /**
     * @param  array{name?: string|null, password: string}  $input
     */
    private function user(Invitation $invitation, array $input): User
    {
        $user = User::query()->where('email', $invitation->email)->first();

        if ($user === null) {
            $user = User::query()->create([
                'name' => (string) ($input['name'] ?? ''),
                'email' => $invitation->email,
                'password' => $input['password'],
            ]);
        } elseif (! Hash::check($input['password'], $user->password)) {
            throw ValidationException::withMessages(['password' => __('auth.invitation.wrong_password')]);
        }

        // The mailed link proves the address.
        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return $user;
    }

    private function membership(Invitation $invitation, User $user): SchoolMembership
    {
        $membership = SchoolMembership::query()
            ->whereBelongsTo($user)
            ->where('role', $invitation->role)
            ->first();

        if ($membership?->status === MembershipStatus::Active) {
            return $membership;
        }

        if ($invitation->role === MembershipRole::Principal
            && SchoolMembership::query()->active()->where('role', MembershipRole::Principal)->exists()) {
            throw ValidationException::withMessages(['password' => __('auth.invitation.principal_taken')]);
        }

        $membership ??= new SchoolMembership(['user_id' => $user->id, 'role' => $invitation->role]);
        $membership->fill([
            'status' => MembershipStatus::Active,
            'started_on' => now()->toDateString(),
            'ended_on' => null,
        ])->save();

        return $membership;
    }
}
