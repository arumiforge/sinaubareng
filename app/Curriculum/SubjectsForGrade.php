<?php

declare(strict_types=1);

namespace App\Curriculum;

use App\Enums\LearningType;
use App\Enums\SubjectKind;
use App\Enums\SubjectSchoolLevel;
use App\Models\CurriculumVersion;
use App\Models\RegionSubject;
use App\Models\School;
use App\Models\SchoolSubject;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use App\Tenancy\SchoolContext;
use Illuminate\Support\Collection;
use LogicException;

/**
 * The intrakurikuler subjects a class of one grade takes at a school (data model,
 * Curriculum):
 *
 * 1. compulsory subjects with an allocation for the grade in the level's structure
 *    version (config curriculum.allocation_versions); a school cannot switch these off;
 * 2. the region's default local content, on unless the school switches it off;
 * 3. whatever else the school switches on in school_subjects (optional content such as
 *    Bahasa Inggris and IPAS in grades 1–2, or Tata Boga), off by default (D-204).
 *
 * A school_subjects row for one grade wins over its every-grade row. Kokurikuler-only
 * subjects (Koding dan KA, D-208) are not class subjects. Runs only for the school in
 * the current context, so school_subjects stays fail-closed (TEN-1).
 */
final readonly class SubjectsForGrade
{
    public function __construct(private SchoolContext $context) {}

    /**
     * @return Collection<int, SubjectOffer> keyed by subject id, in catalogue order
     */
    public function forSchool(School $school, int $grade): Collection
    {
        if ($this->context->schoolId() !== $school->id) {
            throw new LogicException('Subjects are resolved only for the school in the current context.');
        }

        /** @var array<int, SubjectOffer> $offers */
        $offers = [];

        foreach ($this->structure($school, $grade) as $allocation) {
            $offers[$allocation->subject_id] = new SubjectOffer($allocation->subject, SubjectOffer::FROM_STRUCTURE, true, $allocation->jp_per_week);
        }

        $choices = $this->schoolChoices($grade);

        foreach (RegionSubject::query()->whereBelongsTo($school->region)->with('subject')->get() as $default) {
            $choice = $choices[$default->subject_id] ?? null;

            if (! isset($offers[$default->subject_id]) && $this->fits($default->subject, $school) && ($choice->enabled ?? true)) {
                $offers[$default->subject_id] = new SubjectOffer($default->subject, SubjectOffer::FROM_REGION, $choice->in_report ?? true, $default->allocation);
            }
        }

        foreach ($choices as $subjectId => $choice) {
            if ($choice->enabled && ! isset($offers[$subjectId]) && $this->fits($choice->subject, $school)) {
                $offers[$subjectId] = new SubjectOffer($choice->subject, SubjectOffer::FROM_SCHOOL, $choice->in_report, null);
            }
        }

        ksort($offers);

        return collect($offers);
    }

    /**
     * @return Collection<int, SubjectAllocation>
     */
    private function structure(School $school, int $grade): Collection
    {
        $code = config("curriculum.allocation_versions.{$school->level->value}");
        $version = is_string($code) ? CurriculumVersion::query()->where('code', $code)->first() : null;

        if ($version === null) {
            return collect();
        }

        return SubjectAllocation::query()
            ->whereBelongsTo($version)
            ->where('grade', $grade)
            ->where('learning_type', LearningType::Intracurricular->value)
            ->with('subject')
            ->get()
            ->filter(fn (SubjectAllocation $allocation): bool => $allocation->subject->kind === SubjectKind::Compulsory && $this->fits($allocation->subject, $school));
    }

    /**
     * The school's rows that apply to the grade, one per subject.
     *
     * @return array<int, SchoolSubject>
     */
    private function schoolChoices(int $grade): array
    {
        $choices = [];
        $rows = SchoolSubject::query()
            ->where(fn ($query) => $query->whereNull('grade')->orWhere('grade', $grade))
            ->orderByRaw('grade is null desc') // every-grade rows first, so a grade's own row wins
            ->with('subject')
            ->get();

        foreach ($rows as $row) {
            $choices[$row->subject_id] = $row;
        }

        return $choices;
    }

    private function fits(Subject $subject, School $school): bool
    {
        return $subject->kind !== SubjectKind::Cocurricular
            && in_array($subject->school_level, [SubjectSchoolLevel::Both, SubjectSchoolLevel::from($school->level->value)], true);
    }
}
