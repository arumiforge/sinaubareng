<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Curriculum\SubjectOffer;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;

/**
 * Creates or changes a class and keeps its teaching assignments in step (D-240):
 *
 * - a new homeroom teacher takes over the subjects the previous one taught in this class
 *   (rows the operator gave to someone else stay as they are);
 * - a new grade drops the rows for subjects the new grade does not take;
 * - then the defaults fill any class-teacher subject still without a teacher.
 */
final readonly class SaveClass
{
    public function __construct(
        private ApplyDefaultAssignments $applyDefaults,
        private ClassSubjects $classSubjects,
    ) {}

    /**
     * @param  array{academic_year_id?: int, grade: int, name: string, homeroom_teacher_id: int|null}  $data
     */
    public function handle(array $data, ?SchoolClass $class = null): SchoolClass
    {
        return DB::transaction(function () use ($data, $class): SchoolClass {
            $class ??= new SchoolClass;
            $previousHomeroom = $class->homeroom_teacher_id;
            $previousGrade = $class->exists ? $class->grade : null;

            $class->fill($data)->save();

            if ($previousHomeroom !== null && $previousHomeroom !== $class->homeroom_teacher_id) {
                $held = $class->teachingAssignments()->where('teacher_id', $previousHomeroom);
                $class->homeroom_teacher_id === null
                    ? $held->delete()
                    : $held->update(['teacher_id' => $class->homeroom_teacher_id]);
            }

            if ($previousGrade !== null && $previousGrade !== $class->grade) {
                $offered = $this->classSubjects->handle($class)->map(fn (SubjectOffer $offer): int => $offer->subject->id)->values()->all();
                $class->teachingAssignments()->whereNotIn('subject_id', $offered)->delete();
            }

            $this->applyDefaults->handle($class);

            return $class;
        });
    }
}
