<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\Timetable\Draft\DayFrame;
use App\Actions\Timetable\Draft\Preferences;
use App\Tenancy\BelongsToSchool;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * A school's day frame and preference switches for the draft generator (D-262).
 *
 * @property int $id
 * @property int $school_id
 * @property array<string, mixed> $day_frame
 * @property array<string, bool> $preferences
 */
#[Fillable(['school_id', 'day_frame', 'preferences'])]
final class TimetableSetting extends Model
{
    use BelongsToSchool;

    /** The school's settings, or the defaults from config/timetable.php. */
    public static function current(): self
    {
        return self::query()->first() ?? new self([
            'day_frame' => config('timetable.day_frame'),
            'preferences' => config('timetable.preferences'),
        ]);
    }

    public function frame(): DayFrame
    {
        return DayFrame::fromArray($this->day_frame);
    }

    public function switches(): Preferences
    {
        return Preferences::fromArray($this->preferences);
    }

    protected function casts(): array
    {
        return [
            'day_frame' => 'array',
            'preferences' => 'array',
        ];
    }
}
