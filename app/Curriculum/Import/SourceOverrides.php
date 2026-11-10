<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

/**
 * Owner-approved corrections applied to a topic at import, without editing the file
 * in docs/data/curriculum/; each correction names its decision.
 * A correction applies only while the source still has the values it was written for,
 * so a fixed or changed source is imported as it is and reported.
 */
final readonly class SourceOverrides
{
    /**
     * Corrections by topic code. Empty since D-366: the D-234 Tata Boga trim is now in
     * the SIGRAK data itself. Add an entry here only for an owner-approved fix that
     * cannot wait for the data to be corrected.
     *
     * @var array<string, array{decision: string, source: array{jp_total: int, week_end: int}, jp_total: int, week_end: int, focus_jp: array<string, int>}>
     */
    private const TOPICS = [];

    /**
     * @param  array<string, array{decision: string, source: array{jp_total: int, week_end: int}, jp_total: int, week_end: int, focus_jp: array<string, int>}>  $topics
     */
    public function __construct(private array $topics = self::TOPICS) {}

    public function apply(TopicInput $input, ImportReport $report): TopicInput
    {
        $override = $this->topics[$input->code] ?? null;

        if ($override === null) {
            return $input;
        }

        $source = $override['source'];

        if ($input->jpTotal !== $source['jp_total'] || $input->weekEnd !== $source['week_end']) {
            $report->note("Topic {$input->code}: the source no longer has {$source['jp_total']} JP to week {$source['week_end']}, so the {$override['decision']} correction is not applied.");

            return $input;
        }

        $focusItems = $input->focusItems;

        foreach ($focusItems as $index => $focus) {
            $focusItems[$index]['estimated_jp'] = $override['focus_jp'][$focus['code']] ?? $focus['estimated_jp'];
        }

        $report->note("Topic {$input->code}: trimmed from {$source['jp_total']} JP over weeks {$input->weekStart}–{$source['week_end']} to {$override['jp_total']} JP over weeks {$input->weekStart}–{$override['week_end']} ({$override['decision']}).");

        return new TopicInput(
            code: $input->code,
            subject: $input->subject,
            grade: $input->grade,
            phase: $input->phase,
            semester: $input->semester,
            elements: $input->elements,
            title: $input->title,
            cpText: $input->cpText,
            atpCode: $input->atpCode,
            orderInSemester: $input->orderInSemester,
            jpTotal: $override['jp_total'],
            jpPerWeek: $input->jpPerWeek,
            weekStart: $input->weekStart,
            weekEnd: $override['week_end'],
            weeklyDistribution: array_slice($input->weeklyDistribution, 0, $override['week_end'] - $input->weekStart + 1),
            references: $input->references,
            curationNotes: $input->curationNotes,
            extras: [...$input->extras ?? [], 'import_override' => [
                'decision' => $override['decision'],
                'source' => [
                    'jp_total' => $input->jpTotal,
                    'week_end' => $input->weekEnd,
                    'focus_jp' => array_column($input->focusItems, 'estimated_jp', 'code'),
                ],
            ]],
            objectives: $input->objectives,
            focusItems: $focusItems,
        );
    }
}
