# P4-24 — Final docs sync (P4-05, P4-14, P4-20, P4-24 combined)

- **Phase:** P4
- **Status:** review
- **Owner:** p4/p4-final-docs-sync
- **Depends on:** P4-03b, P4-13 (D-252…D-328), P4-16…P4-18 (D-329…D-351), P4-21 (D-352…D-359), owner answers D-368…D-371
- **Can run in parallel with:** P4-22, P4-23, P5-03i/j and the D-355 build (other sessions; no shared files except the PROGRESS rows)

## Goal

Bring `docs/final/` (except `research-digest.md`) in line with every accepted decision not yet applied there, translate the changed lines into `docs/pdf/src/`, and rebuild the Indonesian PDFs. One task replaces the four doc-update tasks P4-05, P4-14, P4-20, and P4-24.

## Read first

- `CLAUDE.md`, `docs/agent/CONTEXT.md`, `docs/agent/WORKFLOW.md` (Docs pipeline), `scripts/docs-pdf/README.md`
- Source lists: `docs/final/developer-handbook.md` §12.1, `docs/drafts/legacy-upgrade-01.md` §4, `docs/drafts/policy-innovation-layers.md` §12–§13
- `docs/agent/DECISIONS.md`: D-210…D-221, D-252…D-328, D-329…D-351, D-352…D-372

## Output

- `docs/final/*.md` (not `research-digest.md`), `docs/pdf/src/*.md`, `docs/pdf/*.pdf`
- Drafts marked as applied: `legacy-upgrade-01.md`, `policy-innovation-layers.md`, `administration-features.md`, `teacher-supervision-instruments.md`, `assessment-landscape.md`, `lkpd-exploration.md`, `internal-roles.md`
- This file

## Done when

- [x] Every item below is applied or marked deferred with a reason
- [x] Changed final docs have their translated lines updated and PDFs rebuilt
- [x] Handbook §12.1 lists only differences that remain (none)
- [x] PR open to `main`

## Checklist

Status: **applied** (in the final doc named), **deferred** (reason given).

### A. Handbook §12.1 (earlier list)

| Item | Decision | Status | Where |
|---|---|---|---|
| *Kehadiran* menu; "murid" in UI copy | D-302, D-210 | applied | product-concept, design-system, export-localization, ifp-classroom, ai-provider-engine, religious-education |
| Offline scope: RPM cache, scan queue, observation entry | D-289, D-349 | applied | product-concept, architecture, data-model |
| `journals.status` `partial`, reasons; derived `meetings.status` | D-267, D-268 | applied | data-model, content-engine |
| `timetable_slots` without teacher column; `kind`, `label` | D-247, D-249 | applied | data-model |
| School calendar copied from the region calendar | D-256 | applied | data-model |
| Approved revisions never pruned | D-282 | applied | data-model, architecture |
| PDF merge for Berkas Administrasi only | D-280 | applied | export-localization (XP-6), content-engine, architecture |
| Margin profiles, duplex, cover, landscape, NIHIL | D-283 | applied | export-localization (XP-39…XP-42) |
| LKPD blocks and `pattern` | D-296, D-329 | applied | content-engine, data-model |
| Bank Soal per-item data, six prints, item library | D-293, D-294 | applied | content-engine, data-model |
| TKA levels | D-041, D-120, D-371 | applied | content-engine, data-model, design-system, export-localization (XP-47) |
| PGK Kategori pair "mendukung/tidak mendukung" dropped | D-045 | applied | content-engine |
| Eleven staff roles with scopes | D-338, D-339 | applied | data-model, architecture, product-concept |
| Add-on mapping set in the console | D-211 | applied | product-concept |
| Scheduler: Supervisor `schedule:work` | D-064 | applied | architecture, server-setup |
| Console model per purpose; console adapters | D-315, D-326 | applied | ai-provider-engine, architecture |
| `ShouldBeUniqueUntilProcessing` + `WithoutOverlapping` | D-050 | applied | ai-provider-engine, architecture |
| `learning_outcomes`; `public_id` on years, classes, memberships | D-226, D-182, D-241 | applied | data-model |
| Support access under the school MoU | D-341 | applied | product-concept, architecture, data-model |
| `curriculum_versions` versioning columns and resolver | D-355 | applied | data-model, architecture, content-engine |
| OMR location (D-287/D-289 vs D-347) | D-369 | applied | content-engine, architecture, ai-provider-engine, handbook §4.6 |

### B. `legacy-upgrade-01.md` §4

| Doc | Status |
|---|---|
| data-model | applied |
| content-engine | applied |
| product-concept | applied |
| export-localization | applied (XP-39…XP-48, LOC-35) |
| architecture | applied |
| ai-provider-engine | applied |
| design-system | applied |
| ifp-classroom | applied |
| `drafts/teacher-supervision-instruments.md` | applied (§5, §6, new §9) |
| `drafts/administration-features.md` | applied (H2, K3, O4, T1) |
| `docs/pdf/` | applied (every changed final doc rebuilt) |

### C. Policy foundation PQ1–PQ8

| PQ | Decision | Status | Where |
|---|---|---|---|
| PQ1 position | D-352 | applied | product-concept |
| PQ2 rule strength | D-353 | applied | product-concept, content-engine, design-system ("Contoh") |
| PQ3 versioning model | D-354 | applied as target (tables `regulations`, `regulation_relations`, `rule_sets`, `rules` later, P5-09 or own slice) | data-model, architecture, product-concept |
| PQ4 minimum before P5-04 | D-355 | applied (`effective_on`, `applies_from_school_year`, `supersedes_id`, resolver) | data-model, architecture, content-engine |
| PQ5 Profil Jenjang | D-356 | applied | data-model, product-concept, religious-education, architecture |
| PQ6 master taxonomy | D-357 | applied (data is P4-22) | product-concept, content-engine, data-model |
| PQ7 Dasar Kebijakan, Cek Keselarasan | D-358 | applied | content-engine, architecture, design-system |
| PQ8 KMA 512/2026 | D-359 (+ D-368) | applied | religious-education, content-engine, handbook §3.7 |

### D. P4-05 and P4-20 scope

| Item | Decision | Status | Where |
|---|---|---|---|
| 2025–2026 rules: murid, rapor 12 components, kokurikuler on 8 DPL, ekskul and Pramuka, JTM | D-210, D-212, D-214, D-216 | applied | product-concept, design-system, data-model |
| P4-04 features (homeroom, principal, e-Rapor/RDM, savings, upcoming list) | D-213…D-220 | applied (detail stays in P4-06/P5-08 design) | product-concept, data-model, content-engine |
| Supervision instruments | D-222…D-225 | applied (one paragraph; detail in the draft) | product-concept |
| LKPD waves | D-329…D-337 | applied | content-engine, product-concept, ifp-classroom |
| Internal roles | D-338…D-343 | applied | product-concept, architecture, data-model |
| Assessment landscape | D-344…D-351 | applied | content-engine, product-concept, data-model, ai-provider-engine |
| Pindai LJK in the browser; handwritten answers | D-369, D-370 | applied | content-engine, architecture, ai-provider-engine, export-localization (XP-44) |
| KBC fields for SD | D-368 | applied | religious-education, content-engine, handbook |

Not changed (nothing stale): `coding-standards.md`, `server-monitoring.md`, `local-setup-wsl2.md`. Not touched by rule: `research-digest.md`.

## Result

Done 2026-11-10 on branch `p4/p4-final-docs-sync`. Eleven final docs changed (product-concept, content-engine, data-model, architecture, server-setup, ai-provider-engine, export-localization, design-system, ifp-classroom, religious-education, developer-handbook), each with its translated lines and PDF in the same commit. Drafts marked as applied.

**Open questions (owner):**
- File names inside the principal's school-wide Berkas ZIP (D-279) versus XP-33 (no teacher names in file names); recorded in `export-localization.md`.
- Menu placement chosen without a decision, to confirm: teachers' Pusat Tindak Lanjut under Hari Ini; Ekstrakurikuler and Tabungan under Kelas Saya (Tabungan in Keuangan); Arsip Tahun Ajaran under the operator's Tahun Ajaran.
- Design-system choices without a decision: status colours for Pengingat and help requests, the "Disahkan" label, which template uses which margin profile, and banning "Absensi" in `lang/id` with a help-search allow-list.

**Follow-ups for other owners:**
- `lang/id` (session B): "Siswa" → "Murid" (e.g. `classes.php`), "Catatan Siswa" → "Catatan Murid", `terms.attendance` → "Kehadiran", banned-words test (LOC-35), new keys listed in the PR.
- `CONTEXT.md` (coordinator): planned module paths `app/Curriculum/Policy/` (PolicyProfileResolver, Profil Jenjang) and `app/Content/Alignment/` (Cek Keselarasan); handbook §12.1 now empty.
- D-314 ("roles stay admin, support, content") is superseded in practice by D-339; the docs follow D-339 and note the code still has three roles.
