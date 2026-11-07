<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\MembershipRole;
use App\Models\Invitation;
use App\Models\User;
use App\Tenancy\SchoolContext;

/**
 * In the school app the operator invites teachers (product concept, Operator flow).
 * The first operator and principal are invited by Tim SIGRAK from the staff console.
 */
final class InvitationPolicy
{
    public function __construct(private readonly SchoolContext $context) {}

    public function viewAny(User $user): bool
    {
        return $this->isOperator();
    }

    public function create(User $user): bool
    {
        return $this->isOperator();
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $this->isOperator()
            && $invitation->school_id === $this->context->schoolId()
            && $invitation->role === MembershipRole::Teacher
            && $invitation->accepted_at === null;
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $this->update($user, $invitation);
    }

    private function isOperator(): bool
    {
        return $this->context->schoolId() !== null && $this->context->role() === MembershipRole::Operator;
    }
}
