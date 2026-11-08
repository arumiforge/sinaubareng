<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TimetableSlotKind;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TimetableSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * A break on Monday by default; `lesson()` makes a lesson row.
 *
 * @extends Factory<TimetableSlot>
 */
final class TimetableSlotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'class_id' => SchoolClass::factory(),
            'school_id' => fn (array $attributes): int => SchoolClass::withoutSchoolScope()->findOrFail($attributes['class_id'])->school_id,
            'weekday' => 1,
            'period_no' => fn (array $attributes): int => TimetableSlot::withoutSchoolScope()
                ->where('class_id', $attributes['class_id'])->where('weekday', $attributes['weekday'])->max('period_no') + 1,
            'starts_at' => '09:00:00',
            'ends_at' => '09:15:00',
            'kind' => TimetableSlotKind::Break,
            'subject_id' => null,
            'label' => null,
            'jp' => 0,
        ];
    }

    public function lesson(Subject $subject, int $jp = 2): static
    {
        return $this->state(['kind' => TimetableSlotKind::Lesson, 'subject_id' => $subject->id, 'jp' => $jp]);
    }

    public function at(int $weekday, string $startsAt, string $endsAt): static
    {
        return $this->state(['weekday' => $weekday, 'starts_at' => $startsAt.':00', 'ends_at' => $endsAt.':00']);
    }
}
