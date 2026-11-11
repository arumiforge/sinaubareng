<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CalendarEventKind;
use App\Enums\ExamType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An event of a region or school calendar. With `class_id` it applies to that class
 * only (D-258). School events are reached only through their SchoolCalendar, which is
 * school-scoped (scoped route binding), so another school's event is a 404.
 *
 * @property int $id
 * @property string $public_id
 * @property int $calendar_id
 * @property int|null $class_id
 * @property CarbonImmutable $starts_on
 * @property CarbonImmutable $ends_on
 * @property CalendarEventKind $kind
 * @property ExamType|null $exam_type
 * @property string $title
 * @property string|null $source_event_uid
 * @property-read SchoolClass|null $schoolClass
 */
#[Fillable(['calendar_id', 'class_id', 'starts_on', 'ends_on', 'kind', 'exam_type', 'title', 'source_event_uid'])]
final class CalendarEvent extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        // A change to a region calendar's events raises its version (D-256).
        $bump = function (self $event): void {
            RegionCalendar::query()->find($event->calendar_id)?->bumpVersion();
        };

        self::saved($bump);
        self::deleted($bump);
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
            'starts_on' => 'immutable_date',
            'ends_on' => 'immutable_date',
            'kind' => CalendarEventKind::class,
            'exam_type' => ExamType::class,
        ];
    }
}
