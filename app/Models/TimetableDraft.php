<?php

declare(strict_types=1);

namespace App\Models;

use App\Tenancy\BelongsToSchool;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A draft timetable for one class or every class of a year (D-262). `result` holds the
 * rows and the unplaced JP per class; nothing is in the timetable until it is saved.
 *
 * @property int $id
 * @property int $school_id
 * @property string $public_id
 * @property int $academic_year_id
 * @property int|null $class_id
 * @property int|null $created_by
 * @property int $seed
 * @property string $status queued|running|ready|failed|saved
 * @property array{classes?: list<array{class: string, name: string, rows: list<array<string, mixed>>, unplaced: list<array{subject: string, jp: int, reason: string}>}>, unplaced_jp?: int, score?: int}|null $result
 * @property-read AcademicYear $academicYear
 * @property-read SchoolClass|null $schoolClass
 */
#[Fillable(['school_id', 'academic_year_id', 'class_id', 'created_by', 'seed', 'status', 'result'])]
final class TimetableDraft extends Model
{
    use BelongsToSchool;
    use HasUuids;

    public const string QUEUED = 'queued';

    public const string RUNNING = 'running';

    public const string READY = 'ready';

    public const string FAILED = 'failed';

    public const string SAVED = 'saved';

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
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    protected function casts(): array
    {
        return [
            'seed' => 'integer',
            'result' => 'array',
        ];
    }
}
