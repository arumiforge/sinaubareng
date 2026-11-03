<?php

declare(strict_types=1);

namespace App\Tenancy\Actions;

use App\Models\SchoolMembership;
use App\Models\User;
use App\Tenancy\SchoolContext;

/**
 * Picks the school and role a signed-in user acts as: the preferred membership when it
 * is still the user's and active, otherwise their oldest active one.
 */
final class ResolveSchoolContext
{
    public function __construct(private readonly SchoolContext $context) {}

    public function handle(User $user, ?int $preferredMembershipId = null): ?SchoolMembership
    {
        // This query is what sets the school scope, so it cannot use it; it is limited to the user's own rows.
        $memberships = SchoolMembership::withoutSchoolScope()
            ->whereBelongsTo($user)
            ->active()
            ->orderBy('id')
            ->get();

        $membership = $memberships->firstWhere('id', $preferredMembershipId) ?? $memberships->first();

        if ($membership === null) {
            $this->context->clear();

            return null;
        }

        $this->context->set($membership->school_id, $membership->role);

        return $membership;
    }
}
