<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Curriculum\SubjectOffer;
use App\Enums\DefaultTeacher;
use App\Models\SchoolClass;
use App\Models\TeachingAssignment;

/**
 * Default teaching assignments (D-032, D-240): the class's subjects whose default is the
 * class teacher go to the homeroom teacher. Subjects for a guru mapel, and every subject
 * of a class without a homeroom teacher, stay without a row until the operator picks a
 * teacher. Rows that already exist are never changed here.
 */
final readonly class ApplyDefaultAssignments
{
    public function __construct(private ClassSubjects $classSubjects) {}

    public function handle(SchoolClass $class): void
    {
        if ($class->homeroom_teacher_id === null) {
            return;
        }

        $assigned = $class->teachingAssignments()->pluck('subject_id')->all();

        $this->classSubjects->handle($class)
            ->filter(fn (SubjectOffer $offer): bool => $offer->subject->default_teacher === DefaultTeacher::ClassTeacher)
            ->reject(fn (SubjectOffer $offer): bool => in_array($offer->subject->id, $assigned, true))
            ->each(fn (SubjectOffer $offer) => TeachingAssignment::query()->create([
                'school_id' => $class->school_id,
                'class_id' => $class->id,
                'subject_id' => $offer->subject->id,
                'teacher_id' => $class->homeroom_teacher_id,
            ]));
    }
}
