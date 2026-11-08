<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Models\AcademicYear;
use Carbon\CarbonImmutable;

/**
 * Creates or changes an academic year. The name follows the start date (`2026/2027`),
 * so it never disagrees with the dates.
 */
final class SaveAcademicYear
{
    /**
     * @param  array{starts_on: string, even_semester_starts_on: string, ends_on: string}  $data
     */
    public function handle(array $data, ?AcademicYear $academicYear = null): AcademicYear
    {
        $academicYear ??= new AcademicYear;
        $academicYear->fill([
            'name' => AcademicYear::nameFor(CarbonImmutable::parse($data['starts_on'])),
            'starts_on' => $data['starts_on'],
            'even_semester_starts_on' => $data['even_semester_starts_on'],
            'ends_on' => $data['ends_on'],
        ])->save();

        return $academicYear;
    }
}
