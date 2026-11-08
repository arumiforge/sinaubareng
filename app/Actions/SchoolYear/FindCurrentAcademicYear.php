<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Models\AcademicYear;

/**
 * The academic year the school is in today; between two years, the next one; with
 * none ahead, the latest one. Reads the current school only (school scope).
 */
final class FindCurrentAcademicYear
{
    public function handle(): ?AcademicYear
    {
        $today = now()->toDateString();

        return AcademicYear::query()->where('starts_on', '<=', $today)->where('ends_on', '>=', $today)->first()
            ?? AcademicYear::query()->where('starts_on', '>', $today)->orderBy('starts_on')->first()
            ?? AcademicYear::query()->orderByDesc('starts_on')->first();
    }
}
