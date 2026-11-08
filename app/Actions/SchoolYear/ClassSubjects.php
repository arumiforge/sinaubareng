<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Curriculum\SubjectOffer;
use App\Curriculum\SubjectsForGrade;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;

/**
 * The subjects a class takes, from its grade and the school's switches (D-230).
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

        return $this->subjectsForGrade->forSchool($school, $class->grade);
    }
}
