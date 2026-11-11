<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TimetableSlotKind;
use App\Tenancy\BelongsToSchool;
use Database\Factories\TimetableSlotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row of a class's weekly timetable: a lesson, or a break, ceremony, or activity
 * (D-247). A lesson's teacher is the class's teaching assignment for its subject (D-249).
 *
 * @property int $id
 * @property int $school_id
 * @property string $public_id
 * @property int $class_id
 * @property int $weekday 1 = Senin … 6 = Sabtu
 * @property int $period_no
 * @property string $starts_at H:i:s
 * @property string $ends_at H:i:s
 * @property TimetableSlotKind $kind
 * @property int|null $subject_id
 * @property string|null $label
 * @property int $jp
 * @property bool $is_locked kept by the draft generator (D-262)
 */
#[Fillable(['school_id', 'class_id', 'weekday', 'period_no', 'starts_at', 'ends_at', 'kind', 'subject_id', 'label', 'jp', 'is_locked'])]
final class TimetableSlot extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<TimetableSlotFactory> */
    use HasFactory;

    use HasUuids;

    /** Data model: 1 JP = 35 minutes for SD. */
    public const int MINUTES_PER_JP = 35;

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
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    protected function casts(): array
    {
        return [
            'weekday' => 'integer',
            'period_no' => 'integer',
            'kind' => TimetableSlotKind::class,
            'jp' => 'integer',
            'is_locked' => 'boolean',
        ];
    }
}
