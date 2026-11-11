<?php

declare(strict_types=1);

namespace App\Curriculum\Policy;

use App\Enums\CurriculumVersionStatus;
use App\Enums\SchoolLevel;
use App\Models\CurriculumVersion;
use App\Models\Region;
use App\Models\School;
use Illuminate\Support\Facades\Cache;

/**
 * Resolves the policy in force from (school year, level, region) (D-354, D-355). It
 * replaces the old `curriculum.allocation_versions` config pointer: for each domain of
 * the Profil Jenjang, the rule set is the listed curriculum version that has started by
 * the school year (`curriculum_versions.applies_from_school_year`) and is not retired,
 * the latest first; an entry for the region's province beats a national one. When none
 * has started yet, the earliest listed one is used. Cached per school year; every
 * `curriculum:import` starts a new cache generation.
 */
final class PolicyProfileResolver
{
    private const string GENERATION_KEY = 'policy-profile:generation';

    /** @var array<string, PolicyProfile> */
    private array $memo = [];

    public function __construct(private readonly LevelProfiles $profiles) {}

    public function forSchool(School $school, string $schoolYear): PolicyProfile
    {
        return $this->resolve($schoolYear, $school->level, $school->region);
    }

    public function resolve(string $schoolYear, SchoolLevel $level, ?Region $region = null): PolicyProfile
    {
        SchoolYear::startYear($schoolYear); // refuses a malformed year early
        $generation = (int) Cache::get(self::GENERATION_KEY, 0);
        $key = "policy-profile:{$generation}:{$schoolYear}:{$level->value}:".($region->id ?? 0);

        if (isset($this->memo[$key])) {
            return $this->memo[$key];
        }

        $ruleSets = Cache::remember($key, now()->addDay(), fn (): array => $this->ruleSets($schoolYear, $level, $region));

        return $this->memo[$key] = new PolicyProfile($schoolYear, $this->profiles->forYear($level, $schoolYear), $region?->id, $ruleSets);
    }

    /** After an import or a profile change: later calls resolve afresh. */
    public function flush(): void
    {
        Cache::forever(self::GENERATION_KEY, (int) Cache::get(self::GENERATION_KEY, 0) + 1);
        $this->memo = [];
    }

    /**
     * @return array<string, string|null>
     */
    private function ruleSets(string $schoolYear, SchoolLevel $level, ?Region $region): array
    {
        $profile = $this->profiles->forYear($level, $schoolYear);
        $ruleSets = [];

        foreach ($profile->domains() as $domain) {
            $entries = array_values(array_filter(
                $profile->ruleSetEntries($domain),
                fn (array $entry): bool => ($entry['region_province'] ?? null) === null || $entry['region_province'] === $region?->province,
            ));
            $versions = CurriculumVersion::query()
                ->whereIn('code', array_column($entries, 'code'))
                ->where('status', '<>', CurriculumVersionStatus::Retired->value)
                ->get()
                ->keyBy('code');

            $candidates = [];

            foreach ($entries as $order => $entry) {
                $version = $versions->get($entry['code']);

                if ($version !== null) {
                    $from = $version->applies_from_school_year ?? $entry['applies_from_school_year'] ?? null;
                    $candidates[] = [
                        'code' => $version->code,
                        'started' => SchoolYear::hasStarted($from, $schoolYear),
                        'from' => $from === null ? 0 : SchoolYear::startYear($from),
                        'regional' => ($entry['region_province'] ?? null) !== null,
                        'order' => $order,
                    ];
                }
            }

            $started = array_filter($candidates, fn (array $candidate): bool => $candidate['started']);
            usort($started, fn (array $a, array $b): int => [$b['regional'], $b['from'], $a['order']] <=> [$a['regional'], $a['from'], $b['order']]);
            usort($candidates, fn (array $a, array $b): int => [$a['from'], $a['order']] <=> [$b['from'], $b['order']]);

            $ruleSets[$domain] = ($started[0] ?? $candidates[0] ?? null)['code'] ?? null;
        }

        return $ruleSets;
    }
}
