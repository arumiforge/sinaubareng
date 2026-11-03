<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Tenancy\BelongsToSchool;
use Database\Factories\SchoolMembershipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A user's role at a school. Roles live here, never on users (data model).
 *
 * @property int $school_id
 * @property MembershipRole $role
 * @property MembershipStatus $status
 */
#[Fillable(['school_id', 'user_id', 'role', 'status', 'started_on', 'ended_on'])]
final class SchoolMembership extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<SchoolMembershipFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', MembershipStatus::Active);
    }

    protected function casts(): array
    {
        return [
            'role' => MembershipRole::class,
            'status' => MembershipStatus::class,
            'started_on' => 'date',
            'ended_on' => 'date',
        ];
    }
}
