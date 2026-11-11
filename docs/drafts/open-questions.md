# Open Questions

- **Status:** draft (owner review)
- **Task:** P1-08 (started); kept up to date by every task
- **Last updated:** 2026-11-11 (questions from P4-22, P4-23, P4-24, and P5-03i…l added)

## Purpose

One list of every unresolved open question from the drafts, task files, and PRs, so the owner can answer them in one pass. Each draft keeps its own *Open questions* list; this file only collects them. When a question is answered, record the decision in `DECISIONS.md`, tick it in the source draft, and remove it here.

**Who answers:** *owner* = a product or business choice only the owner can make · *team* = the development team finds out (testing, research, checking a service) · *owner + team* = the team prepares, the owner decides.

## Business

| Question | Source | Who answers |
|---|---|---|
| Present the short version to principals and supervisors, and record their objections | [product-concept.md](../final/product-concept.md#open-questions) | owner |

## Brand

| Question | Source | Who answers |
|---|---|---|
| Final name: check "Sigrak" (and backup "Kapur") in PDKI classes 9, 41, 42, then register the .id and .com domains together | [product-concept.md](../final/product-concept.md#open-questions) | owner + team |
| Logo: brief a designer (colour, single-colour, negative, minimum 16 px) | [product-concept.md](../final/product-concept.md#open-questions) | owner |

## Product and navigation

| Question | Source | Who answers |
|---|---|---|
| Test the five-tab menu with 5 class teachers and 2 subject teachers on a click prototype (no real data) | [product-concept.md](../final/product-concept.md#open-questions) | team (owner recruits the teachers) |

## Content engine

| Question | Source | Who answers |
|---|---|---|
| Exact step split for the RPM template, tuned against real output sizes | [content-engine.md](../final/content-engine.md#open-questions) | team |

## AI

| Question | Source | Who answers |
|---|---|---|
| Which 429 detail fields separate per-minute from per-day limits (check real responses). Optional: v1 uses the daily counter instead (D-128) | [ai-provider-engine.md](../final/ai-provider-engine.md#open-questions) | team |

## Data model

| Question | Source | Who answers |
|---|---|---|
| Report card export format required by e-Rapor (columns and file type) | [data-model.md](../final/data-model.md#open-questions) | owner (later, D-144) |

## Religion and curriculum

| Question | Source | Who answers |
|---|---|---|
| An official-channel copy of SK Dirjen Pendis 9941/2025. Low priority: a second copy from another source (2026-10-31) has identical text, but neither comes from kemenag.go.id | [religious-education.md](../final/religious-education.md#open-questions) | owner + team |
| SD religion JP: confirm that the national structure has one "Pendidikan Agama dan Budi Pekerti" row (3 JP per week) for every religion | [religious-education.md](../final/religious-education.md#sd-six-religion-subjects-d-034) | team |
| Known curriculum data issues: Matematika grade 4 semester 2 (80 of 90 JP), Keterampilan Tata Boga target, TP↔fokus links | [data-model.md](../final/data-model.md#curriculum) | team, with the curriculum owner |

The MI allocation question (official copy of KMA 1503/2025) is closed by D-038.

## TKA

| Question | Source | Who answers |
|---|---|---|
| TKA 2028 schedule for SD/MI, once published, to time the TKA release (D-162) | [product-concept.md](../final/product-concept.md#open-questions) | team (watch for the announcement) |

## Regulations and terms (P4-23, 2026-11-11)

| Question | Source | Who answers |
|---|---|---|
| Replace the two mirror copies (Permendikdasmen 26/2025, Permendikbudristek 21/2022) with JDIH copies when they can be fetched, and re-hash (owner chose to keep the mirrors until then) | [P4-23 task](../agent/tasks/P4-23-missing-regulations.md) | team |
| Permendikdasmen 6/2026 Pasal 18(3) gives SD class duties to the *guru kelas* and names "wali kelas" only for SMP/SMA/SMK, while SIGRAK says "wali kelas" for SD/MI (also in the JTM rule of 11/2025, D-212). Keep "wali kelas" in the UI, switch SD to "guru kelas", or show both? | [administration-features.md](administration-features.md) | owner |

## Docs consolidation (P4-24, 2026-11-11)

| Question | Source | Who answers |
|---|---|---|
| File names inside the principal's school-wide Berkas ZIP (D-279) clash with XP-33, which forbids teacher names in file names. Which rule wins? | [export-localization.md](../final/export-localization.md), [P4-24 task](../agent/tasks/P4-24-final-docs-sync.md) | owner |
| Menu placements written without a decision: teachers' Pusat Tindak Lanjut under Hari Ini; Ekstrakurikuler and Tabungan under Kelas Saya (Tabungan in Keuangan); Arsip Tahun Ajaran under the operator's Tahun Ajaran. Confirm or move | [product-concept.md](../final/product-concept.md) | owner |
| Design-system choices without a decision: status colours for Pengingat and help requests, the "Disahkan" label, which template uses which margin profile | [design-system.md](../final/design-system.md) | owner |
| Ban "Absensi" in `lang/id` (LOC-35, with a help-search allow-list)? Today the banned-words test covers siswa, teguran, tunggakan, kurang bayar, belum lunas, tanda tangan elektronik, not "Absensi" | [design-system.md](../final/design-system.md), PR #72 Q12 | owner |
| Confirm that the later decisions win where they contradict earlier ones, as the docs now assume: D-339 over D-314 (eleven staff roles, not three), D-339 over D-315 (the owner, not an admin, sets AI models), D-216 over D-161 (ekstrakurikuler in v1) | [P4-24 task](../agent/tasks/P4-24-final-docs-sync.md) | owner |
| Column names for tables described only in words (support sessions, Berkas jobs, school and user settings, leger state, observation results, Konteks Daerah, console tables) | [data-model.md](../final/data-model.md) | team (in each build slice) |

## Calendar and timetable (P5-03i, P5-03j, 2026-11-11)

Defaults in use until answered; details in [the P5-03 task file](../agent/tasks/P5-03-foundation.md).

| Question | Default in use | Who answers |
|---|---|---|
| Do STS/SAS days count as effective days? D-259 says no; the Jawa Tengah guide (Lampiran XIII) counts the SAS and end-of-year assessment weeks as effective and takes out only TKA days | D-259: exam days are not effective. Region data keeps SAS weeks as notes, so the region total stays 254; a school that enters SAS events gets a lower count | owner |
| Who may edit the calendar? | Operator: everything. Wali kelas: events of their own class. Guru mapel and principal: read only | owner (principal role) |
| Saving a draft timetable replaces every unlocked row of each class in the draft | As stated, after a confirm dialog | owner |
| Jawa Tengah May and June 2027: Lampiran XIII takes out a kelulusan day in May that SD/MI do not have | Kelulusan on 7 June (Pasal 22); year and semester totals match the guide, May and June differ by one day | Tim Validasi |
| `effective_on` of KMA 1503/2025, Kepka BKPDM 020/2026, SK Dirjen Pendis 9941/2025 | NULL; `applies_from_school_year` follows D-354 | Tim Validasi |
| Idul Fitri and Idul Adha 2027 depend on the Menteri Agama after sidang isbat | SKB dates, not marked provisional | team (recheck in 2027) |
| When does a region calendar become visible to schools? | Imported as `draft`; schools see only `active`; a console user activates it after sign-off | Tim Validasi |
| A class with no lessons on its timetable yet: which days count? | The region calendar's school days per week, else 6, with a note | team |
| A day that is both a holiday and an assessment or activity | Counted once: Libur, then Asesmen, then Kegiatan | team |
| A school year before any listed rule set has started | The resolver uses the earliest listed rule set (D-378) | team |
| Draft generator details: same breaks every day; fixed rows (Upacara) come from locked rows; optional content is not placed; the religion group gets the JP all religions share; soft-switch weights fixed in code | As listed | team |

## Taxonomy and registry (P4-22, 2026-11-11)

| Question | Source | Who answers |
|---|---|---|
| Review the whole taxonomy (326 nodes) and the regulation registry, all `status: draft` (D-113) | [docs/data/taxonomy/](../data/taxonomy/README.md) | Tim Validasi |
| Two Profil Pelajar Pancasila → 8 DPL links are `approximate`: berkebinekaan global → kewargaan, bergotong royong → kolaborasi | [P4-22 task](../agent/tasks/P4-22-taxonomy-seed.md) | Tim Validasi |
| DPL sub-dimensions, CP element codes, the region code list, and articles missing where only a mirror or summary was read | [P4-22 task](../agent/tasks/P4-22-taxonomy-seed.md) | team, after P4-32 |
| Review the new SD/MI extracts in `docs/data/regulations/` (10/2025, 12/2025, 26/2025, 21/2022, 6/2026, CP decree index) and the calendar data in `docs/data/calendar/` | [regulations README](../data/regulations/README.md), [calendar README](../data/calendar/README.md) | Tim Validasi |

## Answered on 2026-11-01

Kokurikuler variant in v1 (already D-014), Laravel Excel 4.x supports Laravel 13 (checked in `server-setup.md`), "Templat" in UI copy (D-151), *Kabar* notice when a generation finishes (D-152), keep the last 5 document revisions (D-153), game palette by a designer (D-154), special-education religion CP in scope (D-155), plans and prices (D-157), add-on features (D-158), Game kelas trial (D-159), 1-week teacher trial (D-160), v1 slice order (D-162). Phase 2 left no open questions.
