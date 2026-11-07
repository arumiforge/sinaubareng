<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Derived from `accepted_at` and `expires_at`; not a column.
 */
enum InvitationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Expired = 'expired';

    public function label(): string
    {
        return __("enums.invitation_status.{$this->value}");
    }
}
