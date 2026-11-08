<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\School;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
final class AcademicYearFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            ...self::datesFor(2026),
        ];
    }

    /** The year starting 1 July of the given year. */
    public function starting(int $year): static
    {
        return $this->state(self::datesFor($year));
    }

    /**
     * @return array{name: string, starts_on: string, even_semester_starts_on: string, ends_on: string}
     */
    private static function datesFor(int $year): array
    {
        $start = CarbonImmutable::create($year, 7, 1);

        return [
            'name' => AcademicYear::nameFor($start),
            'starts_on' => $start->toDateString(),
            'even_semester_starts_on' => $start->addMonths(6)->toDateString(),
            'ends_on' => $start->addYear()->subDay()->toDateString(),
        ];
    }
}
