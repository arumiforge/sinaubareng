<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

/**
 * Owner-approved corrections applied to a topic at import. The source file in
 * docs/data/curriculum/ stays exactly as received; each correction names its decision.
 * A correction applies only while the source still has the values it was written for,
 * so a fixed or changed source is imported as it is and reported.
 */
final readonly class SourceOverrides
{
    /**
     * D-234: Keterampilan Tata Boga grade 6 semester 2 has 34 JP over weeks 1–17, but
     * that semester has 14 effective weeks. It is trimmed to 28 JP (14 weeks × 2 JP).
     *
     * @var array<string, array{decision: string, source: array{jp_total: int, week_end: int}, jp_total: int, week_end: int, focus_jp: array<string, int>}>
     */
    private const TOPICS = [
        'KM-SD-2026-R2-KTB-K6-04' => [
            'decision' => 'D-234',
            'source' => ['jp_total' => 34, 'week_end' => 17],
            'jp_total' => 28,
            'week_end' => 14,
            'focus_jp' => ['FM-KTB-K6-04-01' => 14, 'FM-KTB-K6-04-02' => 14],
        ],
    ];

    public function apply(TopicInput $input, ImportReport $report): TopicInput
    {
        $override = self::TOPICS[$input->code] ?? null;

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
            legacyCodes: $input->legacyCodes,
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
            audit: $input->audit,
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
