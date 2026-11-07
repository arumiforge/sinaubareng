<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Enums\MembershipRole;
use App\Tenancy\BelongsToSchool;
use Carbon\CarbonImmutable;
use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The only way into a school (data model, Accounts and schools). The mailed link
 * holds a token; only its SHA-256 hash is stored.
 *
 * @property int $school_id
 * @property string $public_id
 * @property string $email
 * @property MembershipRole $role
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $accepted_at
 */
#[Fillable(['school_id', 'email', 'role', 'token_hash', 'expires_at', 'accepted_at', 'invited_by', 'invited_by_staff_id'])]
#[Hidden(['token_hash'])]
final class Invitation extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    use HasUuids;

    /** Days an invitation link stays valid. */
    public const VALID_DAYS = 7;

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function status(): InvitationStatus
    {
        return match (true) {
            $this->accepted_at !== null => InvitationStatus::Accepted,
            $this->expires_at->isPast() => InvitationStatus::Expired,
            default => InvitationStatus::Pending,
        };
    }

    public function isPending(): bool
    {
        return $this->status() === InvitationStatus::Pending;
    }

    /**
     * Not yet accepted; may be expired (resending renews it).
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function open(Builder $query): void
    {
        $query->whereNull('accepted_at');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * @return BelongsTo<StaffUser, $this>
     */
    public function staffInviter(): BelongsTo
    {
        return $this->belongsTo(StaffUser::class, 'invited_by_staff_id');
    }

    protected function casts(): array
    {
        return [
            'role' => MembershipRole::class,
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }
}
