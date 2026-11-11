<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CalendarChangeAction;
use App\Enums\CalendarChangeStatus;
use App\Tenancy\BelongsToSchool;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A calendar change proposed by a wali kelas, guru mapel, or principal, waiting for the
 * operator (D-380). The payload holds the proposed event fields.
 *
 * @property int $id
 * @property int $school_id
 * @property string $public_id
 * @property int $calendar_id
 * @property int|null $requested_by
 * @property CalendarChangeAction $action
 * @property int|null $event_id
 * @property array{title: string, kind: string, exam_type: string|null, starts_on: string, ends_on: string, class_id: int|null}|null $payload
 * @property string|null $reason
 * @property CalendarChangeStatus $status
 * @property int|null $decided_by
 * @property CarbonImmutable|null $decided_at
 * @property string|null $decision_note
 * @property-read CalendarEvent|null $event
 * @property-read User|null $requester
 */
#[Fillable(['school_id', 'calendar_id', 'requested_by', 'action', 'event_id', 'payload', 'reason', 'status', 'decided_by', 'decided_at', 'decision_note'])]
final class CalendarChangeRequest extends Model
{
    use BelongsToSchool;
    use HasUuids;

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

    /**
     * @return BelongsTo<CalendarEvent, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class, 'event_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    protected function casts(): array
    {
        return [
            'action' => CalendarChangeAction::class,
            'status' => CalendarChangeStatus::class,
            'payload' => 'array',
            'decided_at' => 'immutable_datetime',
        ];
    }
}
