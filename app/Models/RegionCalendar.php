<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CalendarStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A region reference calendar built by Tim SIGRAK from the dinas kaldik (D-256, D-257).
 * A province-level region's calendar serves every regency of the province that has no
 * calendar of its own (D-323). Rows of `calendars` with a region owner.
 *
 * @property int $id
 * @property string $public_id
 * @property int $region_id
 * @property string $academic_year
 * @property string|null $code
 * @property string|null $name
 * @property CalendarStatus $status
 * @property int|null $school_days_per_week
 * @property CarbonImmutable|null $starts_on
 * @property CarbonImmutable|null $even_semester_starts_on
 * @property CarbonImmutable|null $ends_on
 * @property int $version
 * @property list<string>|null $joint_leave_school_days
 * @property array<string, mixed>|null $meta
 * @property-read Region $region
 */
#[Fillable(['region_id', 'academic_year', 'code', 'name', 'status', 'school_days_per_week', 'starts_on', 'even_semester_starts_on', 'ends_on', 'version', 'joint_leave_school_days', 'meta'])]
final class RegionCalendar extends Model
{
    use HasUuids;

    protected $table = 'calendars';

    protected static function booted(): void
    {
        static::addGlobalScope('region', fn (Builder $query) => $query->whereNotNull('calendars.region_id'));

        // Schools hear about every change that affects their days (D-256).
        static::updating(function (self $calendar): void {
            if ($calendar->isDirty(self::TRACKED) && ! $calendar->isDirty('version')) {
                $calendar->version = (int) $calendar->getOriginal('version') + 1;
            }
        });
    }

    /** Fields whose change is offered to schools that copied the calendar. */
    public const array TRACKED = ['starts_on', 'even_semester_starts_on', 'ends_on', 'school_days_per_week', 'joint_leave_school_days'];

    /**
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    /**
     * The active calendar a school in `$region` copies for a year: the regency's own,
     * else the province's (D-323).
     */
    public static function forRegion(Region $region, string $academicYear): ?self
    {
        $own = self::query()->where('region_id', $region->id)->where('academic_year', $academicYear)->where('status', CalendarStatus::Active->value)->first();

        if ($own !== null || $region->regency === null) {
            return $own;
        }

        return self::query()
            ->whereHas('region', fn (Builder $query) => $query->where('province', $region->province)->whereNull('regency'))
            ->where('academic_year', $academicYear)
            ->where('status', CalendarStatus::Active->value)
            ->first();
    }

    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * @return HasMany<CalendarEvent, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'calendar_id');
    }

    /** Called after every change to the calendar's events, so schools see an update (D-256). */
    public function bumpVersion(): void
    {
        self::query()->whereKey($this->id)->increment('version');
    }

    protected function casts(): array
    {
        return [
            'status' => CalendarStatus::class,
            'school_days_per_week' => 'integer',
            'starts_on' => 'immutable_date',
            'even_semester_starts_on' => 'immutable_date',
            'ends_on' => 'immutable_date',
            'version' => 'integer',
            'joint_leave_school_days' => 'array',
            'meta' => 'array',
        ];
    }
}
