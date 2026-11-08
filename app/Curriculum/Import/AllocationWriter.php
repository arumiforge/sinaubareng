<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use App\Enums\LearningType;
use App\Enums\Phase;
use App\Models\CurriculumVersion;
use App\Models\Subject;
use App\Models\SubjectAllocation;

/**
 * Writes subject_allocations rows. Regulations print JP per year only, so the week
 * and semester figures are derived: semester 1 has 18 effective weeks and semester 2
 * the rest (18, or 14 in grade 6), as in KM-SD-2026-R2.
 */
final readonly class AllocationWriter
{
    public const FIRST_SEMESTER_WEEKS = 18;

    public function __construct(private RowWriter $rows) {}

    /**
     * @return SubjectAllocation|null null when the yearly JP does not split into whole JP per week
     */
    public function writeYearly(CurriculumVersion $version, Subject $subject, int $grade, Phase $phase, LearningType $type, int $jpPerYear, int $weeksPerYear, ImportReport $report): ?SubjectAllocation
    {
        if ($weeksPerYear <= self::FIRST_SEMESTER_WEEKS || $jpPerYear % $weeksPerYear !== 0) {
            $report->mismatch("{$subject->code} grade {$grade} {$type->value}: {$jpPerYear} JP per year over {$weeksPerYear} weeks is not a whole number of JP per week; row skipped.");

            return null;
        }

        $perWeek = intdiv($jpPerYear, $weeksPerYear);
        $secondWeeks = $weeksPerYear - self::FIRST_SEMESTER_WEEKS;

        return $this->write($version, $subject, $grade, $phase, $type, [
            'jp_per_week' => $perWeek,
            'weeks_per_year' => $weeksPerYear,
            's1_jp' => $perWeek * self::FIRST_SEMESTER_WEEKS,
            's1_effective_weeks' => self::FIRST_SEMESTER_WEEKS,
            's2_jp' => $perWeek * $secondWeeks,
            's2_effective_weeks' => $secondWeeks,
        ], $report);
    }

    /**
     * @param  array{jp_per_week: int, weeks_per_year: int, s1_jp: int, s1_effective_weeks: int, s2_jp: int, s2_effective_weeks: int}  $figures
     */
    public function write(CurriculumVersion $version, Subject $subject, int $grade, Phase $phase, LearningType $type, array $figures, ImportReport $report): SubjectAllocation
    {
        return $this->rows->upsert(SubjectAllocation::class, [
            'curriculum_version_id' => $version->id,
            'subject_id' => $subject->id,
            'grade' => $grade,
            'learning_type' => $type->value,
        ], ['phase' => $phase->value, ...$figures], $report);
    }

    /**
     * Rows of the version the file no longer has are kept (later data may point at
     * them) and reported.
     *
     * @param  list<int>  $writtenIds
     */
    public function reportStale(CurriculumVersion $version, array $writtenIds, ImportReport $report): void
    {
        SubjectAllocation::query()->whereBelongsTo($version)->whereNotIn('id', $writtenIds)->with('subject')
            ->each(function (SubjectAllocation $row) use ($report): void {
                $report->mismatch("Allocation {$row->subject->code} grade {$row->grade} {$row->learning_type->value} is in the database but not in the file; it is kept.");
            });
    }
}
