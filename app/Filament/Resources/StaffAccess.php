<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\SchoolLevel;
use App\Enums\StaffRole;
use App\Models\StaffUser;

/**
 * Which staff roles may open a console resource (data model: staff_users.role).
 */
final class StaffAccess
{
    public static function allows(StaffRole ...$roles): bool
    {
        $staff = auth('staff')->user();

        return $staff instanceof StaffUser && $staff->hasStaffRole(...$roles);
    }

    /**
     * Enum cases as select options with their Indonesian labels.
     *
     * @param  class-string<SchoolLevel|StaffRole>  $enum
     * @return array<string, string>
     */
    public static function options(string $enum): array
    {
        return collect($enum::cases())
            ->mapWithKeys(fn ($case): array => [$case->value => $case->label()])
            ->all();
    }
}
