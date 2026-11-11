<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Curriculum\SubjectOffer;
use App\Curriculum\SubjectsForGrade;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;

/**
 * The subjects a class takes, from its grade, the rule sets of its school year (D-355),
 * and the school's switches (D-230).
 */
final readonly class ClassSubjects
{
    public function __construct(private SubjectsForGrade $subjectsForGrade) {}

    /**
     * @return Collection<int, SubjectOffer> keyed by subject id, in catalogue order
     */
    public function handle(SchoolClass $class): Collection
    {
        $school = School::query()->findOrFail($class->school_id);
        $year = AcademicYear::query()->whereKey($class->academic_year_id)->value('name');

        return $this->subjectsForGrade->forSchool($school, $class->grade, is_string($year) ? $year : null);
    }
}
