<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

/**
 * What the generator gets for one class: free cells of the frame (locked rows taken
 * out), the JP to place, rows that stay (locked rows and breaks), and JP that cannot be
 * placed whatever happens (no teacher yet).
 */
final readonly class ClassPlan
{
    /**
     * @param  list<Cell>  $cells
     * @param  list<Demand>  $demands
     * @param  list<array<string, mixed>>  $fixedRows
     * @param  list<array{subject: string, jp: int, reason: string}>  $unplaceable
     */
    public function __construct(
        public int $classId,
        public string $publicId,
        public string $name,
        public array $cells,
        public array $demands,
        public array $fixedRows,
        public array $unplaceable,
    ) {}
}
