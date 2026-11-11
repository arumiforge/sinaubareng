<?php

declare(strict_types=1);

namespace App\Curriculum\Policy;

/**
 * One version of a Profil Jenjang (D-356): what differs between levels, as data. Code
 * asks the profile and never checks the level itself.
 */
final readonly class LevelProfile
{
    /**
     * @param  array<string, mixed>  $data  one entry of `versions[]` in docs/data/profiles/<level>.json
     */
    public function __construct(public string $level, public array $data) {}

    public function code(): string
    {
        return (string) $this->data['code'];
    }

    public function appliesFrom(): string
    {
        return (string) $this->data['applies_from_school_year'];
    }

    /** @return array{code: string, name: string} */
    public function ministry(): array
    {
        /** @var array{code: string, name: string} $ministry */
        $ministry = $this->data['ministry'];

        return $ministry;
    }

    /**
     * The rule-set entries of a domain, e.g. `structure_jp`, `cp`, `tp_atp_defaults`.
     *
     * @return list<array{code: string, effective_on?: string|null, applies_from_school_year?: string|null, applies_to_school_year?: string|null, region_province?: string|null, supersedes?: string|null}>
     */
    public function ruleSetEntries(string $domain): array
    {
        return array_values($this->data['rule_sets'][$domain] ?? []);
    }

    /** @return list<string> */
    public function domains(): array
    {
        return array_keys($this->data['rule_sets'] ?? []);
    }

    /** @return list<string> e.g. dpl_8, kaih_7, kbc */
    public function characterFrameworks(): array
    {
        return array_values($this->data['character_frameworks'] ?? []);
    }

    public function hasCharacterFramework(string $framework): bool
    {
        return in_array($framework, $this->characterFrameworks(), true);
    }

    /**
     * RPM fields beyond the core, e.g. `kbc` (D-126, D-368).
     *
     * @return array<string, array{enabled: bool, switchable_by_admin: bool, fields: list<string>}>
     */
    public function rpmExtraFields(): array
    {
        return $this->data['rpm_extra_fields'] ?? [];
    }

    public function reportExportTarget(): string
    {
        return (string) $this->data['report_export_target'];
    }

    /** Wali kelas JTM default (D-212); a school may change it. */
    /** UI and print term for the teacher who holds one class (D-383). */
    public function homeroomTeacherTerm(): string
    {
        return (string) ($this->data['homeroom_teacher_term'] ?? 'Wali kelas');
    }

    public function homeroomJtmDefault(): int
    {
        return (int) $this->data['homeroom_jtm_default'];
    }

    /** @return list<array{code: string, status: string}> */
    public function externalAssessments(): array
    {
        return array_values($this->data['external_assessments'] ?? []);
    }

    public function calendarSource(): string
    {
        return (string) $this->data['calendar_source'];
    }

    public function dataOfRecord(): string
    {
        return (string) $this->data['data_of_record'];
    }
}
