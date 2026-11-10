<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use App\Enums\Phase;
use App\Models\Subject;

/**
 * One topic with its TP and fokus, read from a SIGRAK curriculum file (D-363).
 */
final readonly class TopicInput
{
    /**
     * @param  list<int>  $weeklyDistribution
     * @param  array<string, mixed>  $references
     * @param  array<string, mixed>|null  $curationNotes
     * @param  array<string, mixed>|null  $extras
     * @param  list<array{code: string, order: int, text: string}>  $objectives
     * @param  list<array{code: string, order: int, title: string, estimated_jp: int, allocation_status: string, objective_codes: list<string>|null}>  $focusItems
     */
    public function __construct(
        public string $code,
        public Subject $subject,
        public int $grade,
        public Phase $phase,
        public int $semester,
        public string $elements,
        public string $title,
        public string $cpText,
        public string $atpCode,
        public int $orderInSemester,
        public int $jpTotal,
        public int $jpPerWeek,
        public int $weekStart,
        public int $weekEnd,
        public array $weeklyDistribution,
        public array $references,
        public ?array $curationNotes,
        public ?array $extras,
        public array $objectives,
        public array $focusItems,
    ) {}
}
