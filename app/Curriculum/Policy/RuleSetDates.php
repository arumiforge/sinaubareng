<?php

declare(strict_types=1);

namespace App\Curriculum\Policy;

use App\Models\CurriculumVersion;

/**
 * Copies `effective_on`, `applies_from_school_year`, and `supersedes_id` from the
 * Profil Jenjang rule-set entries onto a curriculum version (D-355). Runs on every
 * `curriculum:import`, so the columns follow the data files.
 */
final readonly class RuleSetDates
{
    public function __construct(private LevelProfiles $profiles) {}

    public function apply(CurriculumVersion $version): void
    {
        foreach ($this->profiles->allRuleSetEntries() as $entry) {
            if ($entry['code'] !== $version->code) {
                continue;
            }

            $supersedes = ($entry['supersedes'] ?? null) === null ? null
                : CurriculumVersion::query()->where('code', $entry['supersedes'])->value('id');

            $version->forceFill([
                'effective_on' => $entry['effective_on'] ?? null,
                'applies_from_school_year' => $entry['applies_from_school_year'] ?? null,
                'supersedes_id' => $supersedes,
            ]);

            return;
        }
    }
}
