<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

/**
 * JP a class still needs for one subject, or for the religion subjects together: they
 * share the same time (D-251), so a group places as one, and every teacher must be free.
 */
final readonly class Demand
{
    /**
     * @param  list<array{subject_id: int, code: string, name: string, teacher_id: int, teacher_name: string}>  $subjects
     */
    public function __construct(
        public string $key,
        public array $subjects,
        public int $jp,
        public bool $isPjok = false,
        public bool $isCore = false,
    ) {}

    /** @return list<int> */
    public function teacherIds(): array
    {
        return array_values(array_unique(array_column($this->subjects, 'teacher_id')));
    }

    public function name(): string
    {
        return implode(', ', array_column($this->subjects, 'name'));
    }
}
