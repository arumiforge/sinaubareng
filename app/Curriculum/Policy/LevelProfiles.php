<?php

declare(strict_types=1);

namespace App\Curriculum\Policy;

use App\Enums\SchoolLevel;
use RuntimeException;

/**
 * Reads the Profil Jenjang files in docs/data/profiles (config curriculum.profiles,
 * D-356). Each file has versions; the one in force for a school year is the latest that
 * has started (D-354).
 */
final class LevelProfiles
{
    /** @var array<string, array<string, mixed>> */
    private array $files = [];

    public function forYear(SchoolLevel $level, string $schoolYear): LevelProfile
    {
        $versions = $this->versions($level);
        $current = null;

        foreach ($versions as $version) {
            if (SchoolYear::hasStarted($version->appliesFrom(), $schoolYear)
                && ($current === null || SchoolYear::startYear($version->appliesFrom()) > SchoolYear::startYear($current->appliesFrom()))) {
                $current = $version;
            }
        }

        // A year before the first profile uses the first one, until older data is loaded.
        return $current ?? $versions[0];
    }

    /**
     * Every rule-set entry of every profile version, for copying the dates onto
     * `curriculum_versions` (D-355).
     *
     * @return list<array{code: string, effective_on?: string|null, applies_from_school_year?: string|null, supersedes?: string|null}>
     */
    public function allRuleSetEntries(): array
    {
        $entries = [];

        foreach (SchoolLevel::cases() as $level) {
            foreach ($this->versions($level) as $version) {
                foreach ($version->domains() as $domain) {
                    array_push($entries, ...$version->ruleSetEntries($domain));
                }
            }
        }

        return $entries;
    }

    /**
     * @return non-empty-list<LevelProfile>
     */
    private function versions(SchoolLevel $level): array
    {
        $data = $this->files[$level->value] ??= $this->read($level);
        $versions = array_map(fn (array $version): LevelProfile => new LevelProfile($level->value, $version), $data['versions'] ?? []);

        if ($versions === []) {
            throw new RuntimeException("The Profil Jenjang for '{$level->value}' has no version.");
        }

        return $versions;
    }

    /**
     * @return array<string, mixed>
     */
    private function read(SchoolLevel $level): array
    {
        $path = base_path(rtrim((string) config('curriculum.profiles'), '/')."/{$level->value}.json");
        $data = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;

        if (! is_array($data) || ($data['level'] ?? null) !== $level->value) {
            throw new RuntimeException("No Profil Jenjang file for '{$level->value}' at {$path}.");
        }

        return $data;
    }
}
