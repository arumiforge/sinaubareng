<?php

declare(strict_types=1);

namespace App\Actions\Timetable;

use App\Models\SchoolClass;
use App\Models\TimetableSlot;
use Illuminate\Support\Facades\DB;

/**
 * Creates or changes one timetable row and renumbers the rows of the days it touches
 * by start time (`period_no`, D-247). The request has already checked the rules.
 */
final class SaveTimetableSlot
{
    /**
     * @param  array{weekday: int, starts_at: string, ends_at: string, kind: string, subject_id: int|null, label: string|null, jp: int}  $data
     */
    public function handle(SchoolClass $class, array $data, ?TimetableSlot $slot = null): TimetableSlot
    {
        return DB::transaction(function () use ($class, $data, $slot): TimetableSlot {
            $slot ??= new TimetableSlot(['class_id' => $class->id, 'school_id' => $class->school_id]);
            $previousWeekday = $slot->exists ? $slot->weekday : null;

            // A temporary number past any real one; renumber() sets the real place.
            $slot->fill([...$data, 'period_no' => 200])->save();

            $this->renumber($class, $slot->weekday);

            if ($previousWeekday !== null && $previousWeekday !== $slot->weekday) {
                $this->renumber($class, $previousWeekday);
            }

            return $slot->refresh();
        });
    }

    public function delete(TimetableSlot $slot): void
    {
        DB::transaction(function () use ($slot): void {
            $slot->delete();
            $class = SchoolClass::query()->findOrFail($slot->class_id);
            $this->renumber($class, $slot->weekday);
        });
    }

    /** Numbers a day's rows 1, 2, … by start time. */
    private function renumber(SchoolClass $class, int $weekday): void
    {
        $ids = $class->timetableSlots()->where('weekday', $weekday)->orderBy('starts_at')->lockForUpdate()->pluck('id');

        // Move every row out of the way first, so the unique (class, weekday, period_no) holds at each step.
        $class->timetableSlots()->where('weekday', $weekday)->where('period_no', '<', 100)->increment('period_no', 100);

        foreach ($ids->values() as $index => $id) {
            TimetableSlot::query()->whereKey($id)->update(['period_no' => $index + 1]);
        }
    }
}
