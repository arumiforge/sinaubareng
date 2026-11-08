<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\SchoolMembership;
use Illuminate\Support\Collection;

/**
 * The current school's active teachers, for the homeroom and subject teacher pickers.
 * Teachers are named by their membership's public id, never a user id (DB-11).
 */
final class TeacherOptions
{
    /**
     * @return Collection<int, SchoolMembership> keyed by user id, by name
     */
    public function handle(): Collection
    {
        return SchoolMembership::query()
            ->where('role', MembershipRole::Teacher)
            ->where('status', MembershipStatus::Active)
            ->with('user:id,name')
            ->get()
            ->sortBy(fn (SchoolMembership $membership): string => $membership->user->name)
            ->keyBy('user_id');
    }

    /**
     * The user behind an active teacher membership of the current school, or NULL.
     */
    public function userId(?string $membershipPublicId): ?int
    {
        if ($membershipPublicId === null) {
            return null;
        }

        return SchoolMembership::query()
            ->where('public_id', $membershipPublicId)
            ->where('role', MembershipRole::Teacher)
            ->where('status', MembershipStatus::Active)
            ->value('user_id');
    }
}
