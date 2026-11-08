<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use App\Models\CurriculumVersion;
use App\Models\FocusItem;
use App\Models\LearningObjective;
use App\Models\Topic;

/**
 * Checks and writes topics with their TP, fokus, and fokus↔TP links. Rows are matched
 * by code and never deleted: a row missing from the file is reported and kept.
 */
final readonly class TopicWriter
{
    public function __construct(private RowWriter $rows) {}

    /**
     * @param  list<TopicInput>  $topics
     * @param  array<string, int>  $semesterTargets  expected JP per "SUBJECT|grade|semester"
     */
    public function write(CurriculumVersion $version, array $topics, array $semesterTargets, ImportReport $report): void
    {
        $this->checkUniqueCodes($topics);
        $semesterSums = [];
        $codes = [];

        foreach ($topics as $input) {
            $this->check($input, $report);
            $key = "{$input->subject->code}|{$input->grade}|{$input->semester}";
            $semesterSums[$key] = ($semesterSums[$key] ?? 0) + $input->jpTotal;
            $codes[] = $input->code;
            $this->writeTopic($version, $input, $report);
        }

        foreach ($semesterSums as $key => $sum) {
            [$subject, $grade, $semester] = explode('|', $key);

            if (! array_key_exists($key, $semesterTargets)) {
                $report->note("{$subject} grade {$grade} semester {$semester}: {$sum} JP of topics, no allocation to compare with.");
            } elseif ($semesterTargets[$key] !== $sum) {
                $report->mismatch("{$subject} grade {$grade} semester {$semester}: topics add up to {$sum} JP, the allocation is {$semesterTargets[$key]} JP.");
            }
        }

        foreach (Topic::query()->whereBelongsTo($version)->whereNotIn('code', $codes)->pluck('code') as $stale) {
            $report->mismatch("Topic {$stale} is in the database but not in the file; it is kept.");
        }
    }

    /**
     * @param  list<TopicInput>  $topics
     */
    private function checkUniqueCodes(array $topics): void
    {
        $seen = [];

        foreach ($topics as $input) {
            $codes = [$input->code, ...array_column($input->objectives, 'code'), ...array_column($input->focusItems, 'code')];

            foreach ($codes as $code) {
                if (isset($seen[$code])) {
                    throw new InvalidCurriculumFile("Code {$code} appears more than once in the file.");
                }

                $seen[$code] = true;
            }
        }
    }

    private function check(TopicInput $input, ImportReport $report): void
    {
        $where = "Topic {$input->code}";
        Grades::checkPhase($input->grade, $input->phase, $where, $report);

        $focusJp = array_sum(array_column($input->focusItems, 'estimated_jp'));

        if ($focusJp !== $input->jpTotal) {
            $report->mismatch("{$where}: fokus JP add up to {$focusJp}, jp_total is {$input->jpTotal}.");
        }

        if ($input->weeklyDistribution !== [] && array_sum($input->weeklyDistribution) !== $input->jpTotal) {
            $report->mismatch("{$where}: weekly distribution adds up to ".array_sum($input->weeklyDistribution).", jp_total is {$input->jpTotal}.");
        }

        if ($input->weeklyDistribution !== [] && count($input->weeklyDistribution) !== $input->weekEnd - $input->weekStart + 1) {
            $report->mismatch("{$where}: weeks {$input->weekStart}–{$input->weekEnd} do not match the ".count($input->weeklyDistribution).' weeks of the distribution.');
        }

        if ($input->weekStart < 1 || $input->weekEnd < $input->weekStart) {
            throw new InvalidCurriculumFile("{$where}: weeks {$input->weekStart}–{$input->weekEnd} are not a valid range.");
        }

        if ($input->objectives === [] || $input->focusItems === []) {
            $report->mismatch("{$where}: has ".count($input->objectives).' TP and '.count($input->focusItems).' fokus.');
        }
    }

    private function writeTopic(CurriculumVersion $version, TopicInput $input, ImportReport $report): void
    {
        $topic = $this->rows->upsert(Topic::class, ['code' => $input->code], [
            'curriculum_version_id' => $version->id,
            'legacy_codes' => $input->legacyCodes,
            'subject_id' => $input->subject->id,
            'grade' => $input->grade,
            'phase' => $input->phase->value,
            'semester' => $input->semester,
            'elements' => $input->elements,
            'title' => $input->title,
            'cp_text' => $input->cpText,
            'atp_code' => $input->atpCode,
            'order_in_semester' => $input->orderInSemester,
            'jp_total' => $input->jpTotal,
            'jp_per_week' => $input->jpPerWeek,
            'week_start' => $input->weekStart,
            'week_end' => $input->weekEnd,
            'weekly_distribution' => $input->weeklyDistribution,
            'references' => $input->references,
            'audit' => $input->audit,
            'extras' => $input->extras,
        ], $report);

        $objectiveIds = [];

        foreach ($input->objectives as $objective) {
            $objectiveIds[$objective['code']] = $this->rows->upsert(LearningObjective::class, ['code' => $objective['code']], [
                'topic_id' => $topic->id, 'order' => $objective['order'], 'text' => $objective['text'],
            ], $report)->id;
        }

        $focusIds = [];

        foreach ($input->focusItems as $focus) {
            $item = $this->rows->upsert(FocusItem::class, ['code' => $focus['code']], [
                'topic_id' => $topic->id, 'order' => $focus['order'], 'title' => $focus['title'],
                'estimated_jp' => $focus['estimated_jp'], 'allocation_status' => $focus['allocation_status'],
            ], $report);
            $focusIds[] = $item->id;

            // Files without links (KM-SD-2026-R2) leave the links a data task filled in alone.
            if ($focus['objective_codes'] !== null) {
                $this->link($item, $focus['objective_codes'], $objectiveIds, $report);
            }
        }

        $this->reportStaleChildren($topic, array_values($objectiveIds), $focusIds, $report);
    }

    /**
     * @param  list<string>  $codes
     * @param  array<string, int>  $objectiveIds  TP of the same topic, by code
     */
    private function link(FocusItem $item, array $codes, array $objectiveIds, ImportReport $report): void
    {
        $ids = [];

        foreach ($codes as $code) {
            if (! isset($objectiveIds[$code])) {
                $report->mismatch("Fokus {$item->code} names TP {$code}, which is not a TP of its topic; link skipped.");

                continue;
            }

            $ids[] = $objectiveIds[$code];
        }

        $changes = $item->learningObjectives()->sync($ids);
        $outcome = match (true) {
            $changes['detached'] === [] && $changes['attached'] === [] => 'unchanged',
            $changes['detached'] === [] && count($changes['attached']) === count(array_unique($ids)) => 'created',
            default => 'updated',
        };

        // Counted per fokus: its set of TP links.
        $report->count('focus_item_objectives', $outcome);
    }

    /**
     * @param  list<int>  $objectiveIds
     * @param  list<int>  $focusIds
     */
    private function reportStaleChildren(Topic $topic, array $objectiveIds, array $focusIds, ImportReport $report): void
    {
        foreach (LearningObjective::query()->whereBelongsTo($topic)->whereNotIn('id', $objectiveIds)->pluck('code') as $code) {
            $report->mismatch("TP {$code} of topic {$topic->code} is in the database but not in the file; it is kept.");
        }

        foreach (FocusItem::query()->whereBelongsTo($topic)->whereNotIn('id', $focusIds)->pluck('code') as $code) {
            $report->mismatch("Fokus {$code} of topic {$topic->code} is in the database but not in the file; it is kept.");
        }
    }
}
