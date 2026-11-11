# Profil Jenjang (draft)

One file per school level (`sd.json`, `mi.json`), read by `App\Curriculum\Policy\LevelProfiles` (D-356). Code reads the profile and never checks the level itself; a new level (SDLB, later SMP/MTs) is a new file. Status `draft` until Tim Validasi SIGRAK signs it off (D-113).

Each file holds `versions[]`. A version applies from `applies_from_school_year` until a later version takes over (D-354), so a profile change never rewrites an earlier school year.

| Key | Meaning | Source |
|---|---|---|
| `ministry` | Ministry the level answers to | §8 of `docs/drafts/policy-innovation-layers.md` |
| `rule_sets` | Per domain, the rule sets (curriculum version codes) with `effective_on` and `applies_from_school_year`. `curriculum:import` copies these dates to `curriculum_versions` (D-355). An entry may carry `region_province` to apply in one province only | D-354, D-355 |
| `religion_subjects` | Which religion subject set the level uses | D-251, `subjects.school_level` |
| `character_frameworks` | `dpl_8`, `kaih_7`, and `kbc` for MI | D-356, D-359 |
| `cocurricular_jp_source` | Which subjects give JP to kokurikuler | §8 (C4) |
| `rpm_extra_fields` | RPM fields beyond the core. KBC: on for MI (D-126), off for SD and switchable by a SIGRAK admin (D-368) | D-126, D-368 |
| `report_export_target` | e-Rapor SD or RDM | D-218 |
| `homeroom_teacher_term` | UI and print term for the teacher who holds one class: "Guru kelas" for SD, "Wali kelas" for MI | D-383 |
| `homeroom_jtm_default` | Wali kelas JTM: 2 for SD, 6 for MI; a school may change it | D-212 |
| `external_assessments` | AN and TKA for SD; TKA for MI, AKMI still to verify | §8 |
| `calendar_source`, `data_of_record` | Provincial kaldik or Kanwil calendar; Dapodik or EMIS | D-256, D-161 |

Dates: `effective_on` is filled only where the regulation file in `docs/data/` states it (Permendikdasmen 13/2025: 15 July 2025). The others are `null` until Tim Validasi checks them; `applies_from_school_year` follows D-354 (a rule set from mid-year applies from the next school year).
