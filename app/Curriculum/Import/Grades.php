<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use App\Enums\Phase;

/**
 * Grade and phase rules shared by the formats: Fase A = grades 1–2, B = 3–4, C = 5–6.
 */
final class Grades
{
    public static function phaseOf(int $grade): Phase
    {
        return match (true) {
            $grade >= 1 && $grade <= 2 => Phase::A,
            $grade >= 3 && $grade <= 4 => Phase::B,
            $grade >= 5 && $grade <= 6 => Phase::C,
            default => throw new InvalidCurriculumFile("Grade {$grade} is outside SD/MI grades 1–6."),
        };
    }

    public static function phase(string $value, string $where): Phase
    {
        return Phase::tryFrom(strtoupper(trim($value)))
            ?? throw new InvalidCurriculumFile("{$where}: phase '{$value}' must be A, B, or C.");
    }

    /**
     * Arabic or Roman grade numbers as the sources print them (1, "1", "I", "VI").
     */
    public static function grade(mixed $value, string $where): int
    {
        $roman = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6];
        $grade = match (true) {
            is_int($value) => $value,
            is_string($value) && ctype_digit($value) => (int) $value,
            is_string($value) => $roman[strtoupper(trim($value))] ?? 0,
            default => 0,
        };

        if ($grade < 1 || $grade > 6) {
            throw new InvalidCurriculumFile("{$where}: grade '".json_encode($value)."' must be 1–6.");
        }

        return $grade;
    }

    public static function checkPhase(int $grade, Phase $phase, string $where, ImportReport $report): void
    {
        if (self::phaseOf($grade) !== $phase) {
            $report->mismatch("{$where}: grade {$grade} is in Fase ".self::phaseOf($grade)->value.", the file says Fase {$phase->value}.");
        }
    }
}
