<?php

declare(strict_types=1);

namespace App\Curriculum\Policy;

/**
 * The policy in force for a school year, level, and region (D-354, D-355): the Profil
 * Jenjang and one rule-set code per domain. Documents store `ruleSets()` so a later
 * rule change never changes them silently.
 */
final readonly class PolicyProfile
{
    /**
     * @param  array<string, string|null>  $ruleSets  domain => curriculum version code
     */
    public function __construct(
        public string $schoolYear,
        public LevelProfile $level,
        public ?int $regionId,
        public array $ruleSets,
    ) {}

    public function ruleSet(string $domain): ?string
    {
        return $this->ruleSets[$domain] ?? null;
    }

    /** @return array<string, string|null> */
    public function ruleSets(): array
    {
        return $this->ruleSets;
    }
}
