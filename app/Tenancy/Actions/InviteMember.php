<?php

declare(strict_types=1);

namespace App\Tenancy\Actions;

use App\Enums\MembershipRole;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\School;
use App\Models\SchoolMembership;
use App\Models\StaffUser;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Invites someone into a school with one role and mails them the link. An open
 * invitation for the same school, email, and role is renewed instead of duplicated,
 * so "send again" and a second invite both give one fresh link.
 */
final class InviteMember
{
    public function __construct(private readonly SchoolContext $context) {}

    public function handle(School $school, string $email, MembershipRole $role, User|StaffUser $inviter): Invitation
    {
        $email = Str::lower(trim($email));

        // Runs for the named school, so the console (no school context) and the operator use the same scoped queries.
        return $this->context->runFor($school->id, function () use ($school, $email, $role, $inviter): Invitation {
            $this->ensureNotMember($email, $role);
            $this->ensurePrincipalSeatFree($role);

            $invitation = Invitation::query()->open()
                ->where('email', $email)
                ->where('role', $role)
                ->first() ?? new Invitation(['email' => $email, 'role' => $role]);

            return $this->send($invitation, $school, $inviter);
        });
    }

    /**
     * Gives an open invitation a new link and expiry, and mails it again.
     */
    public function resend(Invitation $invitation, User|StaffUser $inviter): Invitation
    {
        if ($invitation->accepted_at !== null) {
            throw ValidationException::withMessages(['invitation' => __('school.errors.invitation_accepted')]);
        }

        $school = School::query()->findOrFail($invitation->school_id);

        return $this->context->runFor($school->id, fn (): Invitation => $this->send($invitation, $school, $inviter));
    }

    private function send(Invitation $invitation, School $school, User|StaffUser $inviter): Invitation
    {
        $token = Str::random(48);

        $invitation->fill([
            'school_id' => $school->id,
            'token_hash' => Invitation::hashToken($token),
            'expires_at' => now()->addDays(Invitation::VALID_DAYS),
            'invited_by' => $inviter instanceof User ? $inviter->id : null,
            'invited_by_staff_id' => $inviter instanceof StaffUser ? $inviter->id : null,
        ])->save();

        Mail::to($invitation->email)->queue(new InvitationMail(
            schoolName: $school->name,
            role: $invitation->role,
            url: route('invitations.accept', ['invitation' => $invitation->public_id, 'token' => $token]),
            expiresAt: $invitation->expires_at,
        ));

        return $invitation;
    }

    private function ensureNotMember(string $email, MembershipRole $role): void
    {
        $isMember = SchoolMembership::query()->active()
            ->where('role', $role)
            ->whereHas('user', fn ($query) => $query->where('email', $email))
            ->exists();

        if ($isMember) {
            throw ValidationException::withMessages(['email' => __('school.errors.already_member')]);
        }
    }

    private function ensurePrincipalSeatFree(MembershipRole $role): void
    {
        if ($role === MembershipRole::Principal && SchoolMembership::query()->active()->where('role', $role)->exists()) {
            throw ValidationException::withMessages(['role' => __('school.errors.principal_taken')]);
        }
    }
}
