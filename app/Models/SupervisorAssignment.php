<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SupervisorAssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Read-only reach of a pengawas/korwil over a school. Not school-owned data: it is
 * read by `user_id` across schools and managed only in the staff console, so it
 * has no school scope.
 */
#[Fillable(['school_id', 'user_id', 'started_on', 'ended_on'])]
final class SupervisorAssignment extends Model
{
    /** @use HasFactory<SupervisorAssignmentFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'started_on' => 'date',
            'ended_on' => 'date',
        ];
    }
}
