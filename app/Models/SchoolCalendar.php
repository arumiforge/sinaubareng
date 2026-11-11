<?php

declare(strict_types=1);

namespace App\Models;

use App\Tenancy\BelongsToSchool;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A school's calendar for one academic year: copied from the region calendar and
 * extended with school and class events (D-256, D-258). Rows of `calendars` with a
 * school owner; the school scope keeps region rows out.
 *
 * @property int $id
 * @property int $school_id
 * @property string $public_id
 * @property string $academic_year
 * @property int|null $source_calendar_id
 * @property int|null $source_version
 * @property list<string>|null $joint_leave_school_days
 * @property-read RegionCalendar|null $sourceCalendar
 */
#[Fillable(['school_id', 'academic_year', 'status', 'source_calendar_id', 'source_version', 'joint_leave_school_days'])]
final class SchoolCalendar extends Model
{
    use BelongsToSchool;
    use HasUuids;

    protected $table = 'calendars';

    protected $attributes = ['status' => 'active'];

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
     * @return BelongsTo<RegionCalendar, $this>
     */
    public function sourceCalendar(): BelongsTo
    {
        return $this->belongsTo(RegionCalendar::class, 'source_calendar_id');
    }

    /**
     * @return HasMany<CalendarEvent, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'calendar_id');
    }

    /** Whether the region calendar changed after the school last applied or skipped its changes (D-256). */
    public function hasRegionUpdate(): bool
    {
        return $this->sourceCalendar !== null && $this->sourceCalendar->version > (int) $this->source_version;
    }

    protected function casts(): array
    {
        return [
            'source_version' => 'integer',
            'joint_leave_school_days' => 'array',
        ];
    }
}
