<?php

declare(strict_types=1);

namespace App\Actions\Timetable;

use App\Enums\TimetableSlotKind;
use App\Models\SchoolClass;
use App\Models\TimetableSlot;

/**
 * Finds a teacher's lessons in other classes of the same academic year that overlap a
 * time on a weekday (D-248). The teacher of a lesson is its class's teaching assignment
 * for the subject (D-249). Reads the current school only (school scope).
 */
final class TimetableClashes
{
    /** The first lesson of `$teacherId` in another class of `$class`'s year that overlaps the time. */
    public function find(int $teacherId, SchoolClass $class, int $weekday, string $startsAt, string $endsAt): ?TimetableSlot
    {
        return TimetableSlot::query()
            ->select('timetable_slots.*')
            ->join('classes', 'classes.id', '=', 'timetable_slots.class_id')
            ->join('teaching_assignments', function ($join): void {
                $join->on('teaching_assignments.class_id', '=', 'timetable_slots.class_id')
                    ->on('teaching_assignments.subject_id', '=', 'timetable_slots.subject_id');
            })
            ->where('teaching_assignments.teacher_id', $teacherId)
            ->where('classes.academic_year_id', $class->academic_year_id)
            ->where('timetable_slots.class_id', '<>', $class->id)
            ->where('timetable_slots.kind', TimetableSlotKind::Lesson->value)
            ->where('timetable_slots.weekday', $weekday)
            ->where('timetable_slots.starts_at', '<', $endsAt)
            ->where('timetable_slots.ends_at', '>', $startsAt)
            ->orderBy('timetable_slots.starts_at')
            ->with('schoolClass:id,name')
            ->first();
    }

    /**
     * The first clash `$teacherId` would have if they taught `$subjectId` in `$class`.
     */
    public function forSubject(int $teacherId, SchoolClass $class, int $subjectId): ?TimetableSlot
    {
        foreach ($class->timetableSlots()->where('subject_id', $subjectId)->get() as $slot) {
            $clash = $this->find($teacherId, $class, $slot->weekday, $slot->starts_at, $slot->ends_at);

            if ($clash !== null) {
                return $clash;
            }
        }

        return null;
    }

    /** The message that names the other class and time (D-248). */
    public function message(TimetableSlot $clash, string $teacherName): string
    {
        return __('timetable.errors.teacher_clash', [
            'teacher' => $teacherName,
            'class' => $clash->schoolClass->name,
            'day' => __("timetable.days.{$clash->weekday}"),
            'time' => substr($clash->starts_at, 0, 5).'–'.substr($clash->ends_at, 0, 5),
        ]);
    }
}
