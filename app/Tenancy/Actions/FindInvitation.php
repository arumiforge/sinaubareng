<?php

declare(strict_types=1);

namespace App\Tenancy\Actions;

use App\Models\Invitation;

/**
 * Finds the invitation behind a mailed link. The person opening it has no session and
 * no school yet, so the lookup skips the school scope; the token is what authorizes
 * it, compared against the stored hash in constant time (D-182).
 */
final class FindInvitation
{
    public function handle(string $publicId, ?string $token): ?Invitation
    {
        if ($token === null || $token === '') {
            return null;
        }

        $invitation = Invitation::withoutSchoolScope()
            ->with('school:id,name')
            ->where('public_id', $publicId)
            ->first();

        if ($invitation === null || ! hash_equals($invitation->getAttribute('token_hash'), Invitation::hashToken($token))) {
            return null;
        }

        return $invitation;
    }
}
