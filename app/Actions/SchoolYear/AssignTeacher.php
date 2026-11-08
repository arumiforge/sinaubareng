<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;

/**
 * The operator picks who teaches a subject in a class, or clears it (D-032).
 */
final class AssignTeacher
{
    public function handle(SchoolClass $class, Subject $subject, ?int $teacherId): void
    {
        if ($teacherId === null) {
            $class->teachingAssignments()->where('subject_id', $subject->id)->delete();

            return;
        }

        TeachingAssignment::query()->updateOrCreate(
            ['class_id' => $class->id, 'subject_id' => $subject->id],
            ['school_id' => $class->school_id, 'teacher_id' => $teacherId],
        );
    }
}
