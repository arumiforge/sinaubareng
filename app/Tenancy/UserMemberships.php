<?php

declare(strict_types=1);

namespace App\Tenancy;

use App\Models\SchoolMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * A user's own memberships across schools. These queries set or switch the school
 * context, so they cannot use the school scope; each is limited to the user's rows.
 */
final class UserMemberships
{
    /**
     * @return Collection<int, SchoolMembership>
     */
    public function active(User $user): Collection
    {
        return SchoolMembership::withoutSchoolScope()
            ->whereBelongsTo($user)
            ->active()
            ->with('school:id,name')
            ->orderBy('id')
            ->get();
    }

    public function findActive(User $user, string $publicId): ?SchoolMembership
    {
        return SchoolMembership::withoutSchoolScope()
            ->whereBelongsTo($user)
            ->active()
            ->where('public_id', $publicId)
            ->with('school:id,name')
            ->first();
    }
}
