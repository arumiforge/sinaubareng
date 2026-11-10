# Progress

Single source of truth for what is happening. Keep it short: move finished phases to `archive/` when they stop being useful.

Status values: `todo` · `in-progress` · `blocked` · `review` · `done`.
Owner = branch name or session label of whoever claimed the task.

## Current phase

**Phases 0–3 are done** (2026-11-01). All approved docs are in `docs/final/` with Indonesian PDFs in `docs/pdf/`. Decisions run D-001…D-234 and D-240…D-372 (D-185…D-202 unused; D-235…D-239 reserved). D-252…D-328 are the legacy app upgrade (P4-13, `docs/drafts/legacy-upgrade-01.md`). No open PRs, no running tasks. Only `main` exists on the remote (D-148). P4-01 (Buku Induk and Dapodik sync) is on hold as upcoming (D-161). **Phase 5 (v1 build) is next:** P5-01 and P5-02 can start now (D-162).

## Open decisions blocking work

- None.

## Phases

### Phase 0 — Repository conventions · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P0-01 | No-attribution rule | done | pw/amazing-hawking-dn7dfo | — | `CLAUDE.md`, `.claude/settings.json` |
| P0-02 | Language, stack, docs structure, agent workflow | done | pw/amazing-hawking-dn7dfo | P0-01 | `CLAUDE.md`, `docs/` |
| P0-03 | Owner commit identity, one `main` branch, Indonesian URLs | done | main | D-148, D-149 | `CLAUDE.md`, `.claude/`, `docs/agent/`, rewritten history |

### Phase 1 — Product concept docs · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P1-01 | Archive owner sources + owner interview | done | pw/eloquent-shannon-9iwufg | — | `docs/sources/`, D-007…D-021, D-023…D-037 |
| P1-02 | Product concept | done | pw/eloquent-shannon-9iwufg | P1-01 | `docs/final/product-concept.md` |
| P1-03 | Content engine (templates, wizard, games, TKA, export) | done | pw/eloquent-shannon-9iwufg | P1-01 | `docs/final/content-engine.md` |
| P1-04 | AI Provider Engine | done | pw/eloquent-shannon-9iwufg | P1-01 | `docs/final/ai-provider-engine.md`, D-022 |
| P1-05 | Data model | done | pw/eloquent-shannon-9iwufg | P1-02…P1-04 | `docs/final/data-model.md` |
| P1-06 | Design system and UI writing | done | pw/eloquent-shannon-9iwufg | P1-01 | `docs/final/design-system.md` |
| P1-07 | Religious education curriculum (SD religions, MI Kemenag) | done | pw/eloquent-shannon-9iwufg | P1-05 | `docs/final/religious-education.md`, `docs/data/curriculum/` |
| P1-08 | Finish KMA 1503/2025 (MI allocation) + collect open questions | done | p1/p1-08-mi-allocation | P1-07 | `docs/data/curriculum/mi-allocation-kma-1503-2025.json`, `docs/drafts/open-questions.md`, D-038 |

Approved 2026-10-31 (D-123); moved to `docs/final/` in P3-01.

### Phase 2 — data and technical docs · done

Ran in parallel sessions and was integrated on `p2/p2-00-integration`. Decisions from the task outputs are D-039…D-085.

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P2-01 | Verify SD religion CP transcription | done | p2/p2-01-verify-religion-cp | — | `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json` |
| P2-02 | Structure MI CP data, retype Arabic | done | p2/p2-02-mi-cp-structure | — | `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` |
| P2-03 | TKA SD/MI framework seed data | done | p2/p2-03-tka-framework-data | — | `docs/data/tka/kerangka-tka-sd.json` |
| P2-04 | Architecture overview | done | p2/p2-04-architecture | — | `docs/final/architecture.md` |
| P2-05 | Server setup guide | done | p2/p2-05-server-setup | — | `docs/final/server-setup.md` |
| P2-06 | Coding standards | done | p2/p2-06-coding-standards | — | `docs/final/coding-standards.md` |
| P2-07 | Export and localization guide | done | p2/p2-07-export-localization | P2-06 | `docs/final/export-localization.md` |
| P2-08 | Server monitoring and alerts | done | p2/p2-08-server-monitoring | P2-05 | `docs/final/server-monitoring.md` |
| P2-09 | TP and ATP for five SD religion subjects | review | p2/p2-01-verify-religion-cp | P2-01 | `docs/data/curriculum/tp-atp-pendidikan-agama-sd-2026.json` |
| P2-10 | TP and ATP for five MI Kemenag subjects | review | p2/p2-01-verify-religion-cp | P2-02, P2-09 | `docs/data/curriculum/tp-atp-pai-bahasa-arab-mi-2026.json` |

### Phase 3 — final docs · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P3-01 | Move approved drafts to final + Indonesian PDFs | done | p3/p3-01-final-docs-pdf | D-123 | `docs/final/*.md`, `docs/pdf/*.pdf` |
| P3-02 | PDF build script + rebuild outdated PDFs | done | p3/p3-02-pdf-build-script | P3-01 | `scripts/docs-pdf/`, `docs/pdf/*.pdf` |
| P3-03 | Move server monitoring to final + Indonesian PDF | done | main | D-143 | `docs/final/server-monitoring.md`, `docs/pdf/server-monitoring.pdf`, `docs/pdf/server-setup.pdf` |

Later candidates (not yet tasks): curriculum data fixes (Matematika K4 S2, Tata Boga target, TP↔fokus links) · TP/ATP/fokus/JP for five religions and five MI subjects (reviewed by Tim Validasi SIGRAK, D-113) · import support for `ALOKASI-MI-KMA-1503-2025`.

### Phase 4 — add-on designs · on hold

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P4-01 | Buku Induk and Dapodik sync design | blocked (upcoming, D-161) | main | D-157, D-158 | `docs/drafts/buku-induk-dapodik.md` |
| P4-02 | Game kelas: IFP learning mode for SD/MI (benchmark + design) | done | sky/inspiring-volta-3bdmuq | D-011, D-014, D-158 | `docs/final/ifp-classroom.md`, `docs/pdf/ifp-classroom.pdf` |
| P4-03 | Official documents (`docs/dokumen-resmi/`) → SD/MI data | done | p4/p4-03-dokumen-resmi-data | — | `docs/data/regulations/`, `docs/drafts/dokumen-resmi-sd-mi.md` |
| P4-03b | Apply P4-03 findings to `docs/final/` + Indonesian PDFs | done | p4/p4-03b-final-docs | P4-03, D-203…D-209 | `docs/final/{content-engine,data-model,product-concept,religious-education}.md`, `docs/pdf/` |
| P4-04 | Class and school administration features for SD/MI (research + recommendations) | done | p4/p4-04-administration-features | D-009, D-158 | `docs/drafts/administration-features.md`, D-210…D-221 |
| P4-05 | (Do together with P4-14, P4-20, P4-24 as one docs consolidation task; also apply D-352…D-372: TKA levels D-371 and Pindai LJK/uraian D-369, D-370 in content-engine and data-model `exam_results`, KBC for SD D-368 in religious-education, and handbook §12.1.) Update final docs and glossary for the 2025–2026 rules and P4-04 choices ("murid", rapor 12 components, ekskul, JTM) + PDFs (the P4-03 §10 list is already applied by P4-03b) | todo | — | D-210…D-221, P4-03b | `docs/final/*.md`, `docs/pdf/` |
| P4-06 | Design: assessment and rapor extensions (KKTP, diagnostic, remedial/enrichment, item analysis, kokurikuler assessment, 12-component rapor, catatan wali kelas, reflection, portfolio, kenaikan, kelulusan) | todo | — | D-213, D-214, D-215, P4-05 | Draft feeding P5-08 |
| P4-07 | Design: class and homeroom extensions (absence warning, class agreement, 7 KAIH, parent messages, home visits, health data, ekskul and Pramuka, tabungan, effective weeks) | todo | — | D-214…D-216, D-219 | Draft feeding P5-07 |
| P4-08 | Design: principal, operator, supervisor administration (supervisi PM, bukti kinerja, pembagian tugas and JTM SK, budaya sekolah aman, mutasi, inventaris sekolah, supervisor recap and mentoring) | todo | — | D-212, D-217, D-221…D-225, P4-12 | Draft feeding P5-09 |
| P4-09 | Design: console mapping of core vs add-on per feature | todo | — | D-211 | Draft feeding P5-10 |
| P4-10 | Design: e-Rapor SD and RDM export | todo | — | D-218, P5-08 | Draft |
| P4-11 | Design: Penyusun KSP and evaluasi KSP (after v1) | todo | — | D-209, D-217 | Draft |
| P4-12 | Teacher supervision instruments for SD/MI (map + first draft, principal → guru kelas, guru mapel, wali kelas) | done | p4/p4-04-administration-features | D-217, D-221 | `docs/drafts/teacher-supervision-instruments.md` |
| P4-13 | Legacy app upgrade decisions: usulan-01 (U-01…U-15) + Part B (B-01…B-16) + walk-through gaps, owner interviews in six parallel sessions | done | claude/review-proposal-coverage-tzqbk4 | D-251 | `docs/drafts/legacy-upgrade-01.md`, `legacy-coordination.md`, D-252…D-328 |
| P4-14 | Apply the legacy app upgrade to `docs/final/` and drafts (list in `legacy-upgrade-01.md` §4) + Indonesian PDFs | todo | — | P4-13 | `docs/final/*.md`, `docs/pdf/` |
| P4-15 | Curate the usulan-01 catalogs (kokurikuler, ekstrakurikuler, class inventory, form options, administration components) into `docs/data/catalogs/` with SIGRAK names and codes, Tim Validasi review pending (D-305, D-367) | done | sky/optimistic-thompson-2qmlfz | — | `docs/data/catalogs/` |
| P4-16 | LKPD exploration: new LKPD forms (print, no-device, digital, hybrid, new tech), tested 2025–2026 Indonesian SD studies, third-party AR/VR embeds, local context, starter-then-adapt model, risks, clash check | done | sky/determined-bardeen-p9fbn7 | D-024, D-296 | `docs/drafts/lkpd-exploration.md` |
| P4-17 | Internal roles for Tim SIGRAK: role catalogue, editable scopes (region, level, field), access matrix, support access, clash check | done | sky/determined-bardeen-p9fbn7 | D-167, D-314 | `docs/drafts/internal-roles.md` |
| P4-18 | Assessment landscape SD/MI (owner's Master Assessment Landscape) translated to SIGRAK features: map vs existing, Peta Asesmen dataset, instrument templates, five-questions wizard, cautions, phasing; input for P4-06 | done | sky/determined-bardeen-p9fbn7 | P5-08 decisions, D-331 | `docs/drafts/assessment-landscape.md` |
| P4-19 | Design: assessment landscape (Peta Asesmen dataset shape, instrument tags, five-questions wizard, D-346 templates, Wave A) | todo | — | D-344…D-351, P4-18 | Draft + `docs/data/assessment/` |
| P4-20 | Apply D-329…D-351 to `docs/final/` (content-engine LKPD, data-model staff roles and assessment tags, architecture console, product-concept) + Indonesian PDFs | todo | — | P4-16…P4-18 | `docs/final/*.md`, `docs/pdf/` |
| P4-21 | Policy foundation and innovation layers: layer model and rule strength, what the government fixes, component classification, regulation versioning, SD/MI Profil Jenjang, master taxonomy | done | sky/optimistic-thompson-2qmlfz | P4-03, P4-04, P4-18 | `docs/drafts/policy-innovation-layers.md` |
| P4-25 | Developer handbook (whole SIGRAK process) + Indonesian PDF (D-361) | done | sky/optimistic-thompson-2qmlfz | D-352…D-360 | `docs/final/developer-handbook.md`, `docs/pdf/developer-handbook.pdf` |
| P4-26 | Research digest of all research so far + Indonesian PDF (D-362) | done | sky/optimistic-thompson-2qmlfz | — | `docs/final/research-digest.md`, `docs/pdf/research-digest.pdf` |
| P4-27 | Remove the legacy brand from current files (D-360) | done | sky/optimistic-thompson-2qmlfz | — | code, data, sources, docs, PDFs |
| P4-28 | SIGRAK curriculum dataset: one format, SIGRAK codes, curation per subject, crosswalk, SIGRAK catalogs (incl. P4-15) | done | sky/optimistic-thompson-2qmlfz | D-140, D-360 (D-363…D-367) | `tasks/P4-28-sigrak-curriculum-dataset.md` |
| P4-29 | Curate the SD core subjects (PP, BIN, MTK, IPAS) with the D-364 checklist (CP vs official decree, TP cover CP, ATP order and JP vs 13/2025, fokus linked to TP, known issues closed, references) | todo | — | P4-23, D-364 | `docs/data/curriculum/sigrak-sd-2026-r1-umum.json` |
| P4-30 | Curate the other SD subjects (SB, PJOK, BIG, BJW, KTB, SSD, KKA) with the D-364 checklist (CP vs official decree, TP cover CP, ATP order and JP vs 13/2025, fokus linked to TP, known issues closed, references) | todo | — | P4-23, D-364 | `docs/data/curriculum/sigrak-sd-2026-r1-umum.json` |
| P4-31 | Curate the religion subjects, SD (PAI, PAKR, PAKT, PAH, PAB, PAKH) and MI (QH, AA, FIK, SKI, BAR), with the D-364 checklist; set `curation.status: curated` with reviewer and date | todo | — | P4-23, D-364 | `docs/data/curriculum/sigrak-*.json` |
| P4-22 | Data: `docs/data/taxonomy/` seed (facets A–H, codes, strengths, crosswalks) + regulation registry JSON, Tim Validasi review | review | p4/p4-22-taxonomy-seed | P4-21 answers | `docs/data/taxonomy/` |
| P4-23 | Get and extract missing regulations (Permendikdasmen 1/2026, 10/2025, 12/2025, 26/2025, 9/2026, 6/2026; base 12/2024, 21/2022, BSKAP 046/H/KR/2025; verify KMA 512/2026) | todo | — | — | `docs/sources/`, `docs/data/regulations/` |
| P4-24 | Apply accepted P4-21 answers to `docs/final/` (product-concept, data-model, architecture, content-engine) + Indonesian PDFs | todo | — | P4-21 answers | `docs/final/*.md`, `docs/pdf/` |

### Phase 5 — v1 build · todo

Order confirmed by D-162. P5-01 and P5-02 run in parallel. Each slice gets its task file, split into smaller tasks, when it starts.

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P5-01 | Project scaffold and CI | done | pw/jolly-fermi-c64iie | — | Laravel app at the repo root, `.github/workflows/` |
| P5-02 | Server, staging, and monitoring setup | todo | — | — | Running VPS (no repo output) |
| P5-03 | Slice 1 — Foundation: auth, invitations, schools and roles, academic year and classes, curriculum import, timetable | in-progress (P5-03i, P5-03j reopened, D-252) | pw/lucid-fermi-pxp4d5 | P5-01 | App code; split in `tasks/P5-03-foundation.md` |
| P5-04 | Slice 2 — Content core: AI provider engine, template engine, RPM, Dokumen Saya, PDF/DOCX export | todo | — | P5-03 (incl. P5-03i) | App code |
| P5-05 | Slice 3 — More templates: Bank Soal, LKPD, Quiz Battle | todo | — | P5-04 | App code |
| P5-06 | Slice 4 — TKA: package template, exam site, sessions, cards, token, results (target: 2028 season) | todo | — | P5-05 | App code |
| P5-07 | Slice 5 — Di Kelas with offline sync, Jurnal from RPM | todo | — | P5-03 | App code |
| P5-08 | Slice 6 — Asesmen: plan, scores, analysis per TP, report cards | todo | — | P5-05 | App code |
| P5-09 | Slice 7 — Portals: principal, operator, supervisor; validation and comments | todo | — | P5-03 | App code |
| P5-10 | Slice 8 — Business: trial, subscriptions, add-ons, manual payment, Mode Contoh | todo | — | P5-03 | App code |

## Start here (next session)

1. Read `CLAUDE.md`, `docs/agent/CONTEXT.md`, and this file. Stack, conventions, and every owner choice are in CONTEXT and `DECISIONS.md`.
2. Approved specs: `docs/final/*.md`. Still-living drafts: `docs/drafts/open-questions.md` (remaining owner questions), `docs/drafts/buku-induk-dapodik.md` (P4-01, on hold, D-161), and `docs/drafts/curriculum-source-check-2026.md` (P5-03g findings). Moved to final on 2026-11-08: `docs/final/ifp-classroom.md` (Game kelas, P4-02) and `docs/final/local-setup-wsl2.md` (local setup, D-166).
3. Data: `docs/data/curriculum/` (CP, MI allocation, the SIGRAK R1 topic files with per-subject curation status, D-363/D-364), `docs/data/catalogs/` (SIGRAK catalogs, D-367), `docs/data/regulations/`, and `docs/data/tka/`.
4. Only `main` exists. Old branches were deleted and the history was rewritten on 2026-11-01 (D-148), so old clones must be re-cloned or reset to `origin/main`. Start any task branch from `main` and delete it after merge.

## Remaining work (not yet tasks)

- **Owner:** Tim Validasi SIGRAK reviews the CP data and the catalogs (`status: draft`, D-113); topics are curated per subject in P4-29…P4-31 (D-364).
- **Docs:** every doc in `docs/final/` has a matching Indonesian PDF, and every translation lives in `docs/pdf/src/` (D-156).
- **Data:** transcribe the special-education religion CP (Lampiran II of BKPDM 020/2026) for the six religions (D-155).
- **Later candidates:** listed under Phase 2 above, plus a dedicated e-Rapor export (D-144).

## Handoff notes

Short notes for the next agent (newest first, max ~10 lines; delete stale ones). Older notes: `archive/handoff-notes-2026-10-30-to-11-01.md`.

- 2026-11-10 — Owner answers recorded: D-368 (KBC fields for SD off, admin can switch on), D-369 (OMR in the teacher's browser, only results sent), D-370 (handwritten answers read on the phone, teacher corrects, text + absen-soal code to AI, after Pindai LJK in P5-08), D-371 (TKA shows Istimewa/Baik/Memadai/Kurang, stores official category + predicate), D-372 (P4-13 done). Next: the owner starts the next wave in new sessions (P4-23, P5-03i/j + D-355/D-356, P4-22, and one docs consolidation task for P4-05/14/20/24).
- 2026-11-10 — P4-28 and P4-15 done (branch `sky/optimistic-thompson-2qmlfz`, PR #68). Curriculum is one SIGRAK format (D-363): `SIGRAK-SD-2026-R1` (`sigrak-sd-2026-r1-umum.json`, `-agama.json`) and `SIGRAK-MI-2026-R1`; the legacy files are gone and the importer refuses their shape. Each subject has `curation.status` (D-364): general SD `converted`, religions `in_review`; none `curated` yet, so curation tasks P4-29…P4-31 (need P4-23 for the official CP) come before P5-04 can show subjects. Old codes only in `crosswalk-legacy-2026.json`, never imported (D-365); migration `2026_11_10_000001` drops `topics.legacy_codes` and renames `audit` → `curation_notes`. SD allocation only from 13/2025 (D-366). Catalogs in `docs/data/catalogs/` (D-367), still draft for Tim Validasi.
- 2026-11-09 — P4-21 done (branch `sky/optimistic-thompson-2qmlfz`): owner accepted PQ1–PQ8 (D-352…D-359) and supplied KMA 512/2026, now the KBC source (open: KBC for SD religion teachers). D-360: the legacy brand is gone from current files (`docs/drafts/legacy-*.md`, `KmSdAllocation`/`KmSdTopics`, sources renamed; history not rewritten). D-361: read `docs/pdf/developer-handbook.pdf` first as a new developer. D-362: check `docs/final/research-digest.md` before any research and add findings after. Next: P4-22 (taxonomy data), P4-23 (missing regulations), P4-24 (final docs), and the D-355 versioning minimum before P5-04.
- 2026-11-09 — P4-16, P4-17, P4-18 done (branch `sky/determined-bardeen-p9fbn7`, PR #66): owner answered every question. D-332…D-337 (LKPD Wave 1 nine patterns, local context, self-hosted H5P/model-viewer, presets per school, all LKPD core, embeds only with a clear licence), D-338…D-343 (11 staff roles active, scopes editable, payments by Keuangan/owner, support views school data under the school MoU with one internal row per session, partner programme, TOTP 2FA for owner/admin reset by a SIGRAK admin), D-344…D-351 (Peta Asesmen dataset, own design task P4-19, new instruments, assessment AI without student data (names read on the device, essay text only), GPK as assignment, offline observation entry, sensitive results like health data, Wave A). Next: P4-19, P4-20.
- 2026-11-09 — P4-13 in review (branch `claude/review-proposal-coverage-tzqbk4`): usulan-01 (`docs/sources/usulan-01/`) and Part B (`docs/drafts/legacy-overlap-adjustments.md`) were checked against the legacy app walk-through session; 9 conflicts and 24 gaps went to six parallel owner interviews. Result: D-252…D-328 and `docs/drafts/legacy-upgrade-01.md` (item → D map, doc changes, task additions). Next: P4-14 (final docs + PDFs), P4-15 (catalogs), P5-03i (calendar; Jawa Tengah kaldik now in `docs/sources/`, D-323), then P5-04.
- 2026-11-09 — P5-03e done (branch `p5/p5-03e-timetable`), so **P5-03 (Foundation) is done**. Jadwal per class at `/kelas/{class}/jadwal` and Kelas Saya > Jadwal at `/kelas-saya/jadwal`, table `timetable_slots`. Owner chose D-247…D-251: break/ceremony/activity rows, teacher clashes refused, teacher from the class's assignments (no `teacher_id` column), JP must match the allocation, religions in parallel. `data-model.md` still lacks these and `learning_outcomes`/`public_id` columns (next data-model update). Next: P5-04 (content core) or P5-07/P5-09/P5-10, which only need P5-03.
- 2026-11-09 — P5-03h done (branch `p5/p5-03h-copy-classes`): Tahun Ajaran can copy the previous year's classes into a new or empty year, one grade up, without teachers or students (D-242). Owner chose: grade 6 not copied (D-243), optional new grade 1 classes (D-244), free names kept (D-245), only into an empty year (D-246). Next: P5-03e (timetable).
- 2026-11-09 — P5-03d done (branch `p5/p5-03d-school-year`): operator pages Tahun Ajaran (`/tahun-ajaran`) and Kelas (`/kelas`), tables `academic_years`, `classes`, `teaching_assignments`. A class's homeroom teacher gets the class-teacher subjects; the operator picks guru mapel per subject (D-240). Years and classes have a `public_id` (D-241). Not built: the `school_subjects` switches and the D-232 muatan lokal warning (with that page later). Next: P5-03e (timetable).
- 2026-11-08 — Owner approvals applied (branch `p5/owner-approvals-2026-11-08`): P5-03f and P5-03g done; P4-02 done and `ifp-classroom.md` plus `local-setup-wsl2.md` moved to `docs/final/` with Indonesian PDFs. D-233: Kode Tayang page at `/pid` on the app host. D-234: `curriculum:import` trims Tata Boga grade 6 semester 2 (`KM-SD-2026-R2-KTB-K6-04`) to 28 JP over weeks 1–14 (`app/Curriculum/Import/SourceOverrides.php`); the source file is unchanged and the import now reports no mismatch. `product-concept.md` and `content-engine.md` follow D-176. Still open for Game kelas: the Android/WebView check on a real IFP (D-179).
- 2026-11-08 — P5-03c done (PR #60): curriculum tables, `learning_outcomes` for CP (D-226), and `php artisan curriculum:import` (D-227…D-230). It loads all curriculum files, KM-SD-2026-R2 included, as `draft` versions. One data mismatch: Tata Boga grade 6 semester 2 (P5-03g C4), now trimmed at import (D-234). Owner: Seni Suara Daerah is school-optional (D-231); the muatan lokal cap warns for now (D-232). After deploy, run `php artisan curriculum:import`. Next: P5-03d.
- 2026-11-08 — P4-03b done (PR #58): `docs/final/` now follows the official 2025 documents and D-204…D-209. Changes: SD allocation version `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`, optional Bahasa Inggris/IPAS grades 1–2, the eight dimensi profil lulusan, RPM INTI steps, kokurikuler plan schema, rapor tables (`report_cards`, `report_extracurriculars`), and the KSP starter template. PDFs rebuilt.
- 2026-11-08 — P4-04 done: `docs/drafts/administration-features.md` lists SD/MI administration features against the 2025–2026 rules and a benchmark. Owner chose D-210…D-221: rules applied, every new feature core with plan mapping in the console, follow-up design tasks P4-05…P4-11. Owner samples received 2026-11-08: the supervision instrument is in `docs/sources/`; the SK sample and two Ruang GTK documents are kept as received. P4-12 done: `docs/drafts/teacher-supervision-instruments.md` maps 18 teacher supervision instruments with first drafts and an MI first draft; owner chose D-222…D-225 (all 18, Ruang GTK 3-level observation, 1–4 reviews, sample bands, principal-only observation, school recap for pengawas, murid feedback as class totals). P4-08 writes the per-item descriptors.
- 2026-11-08 — P4-03 done (PR #56): SD/MI data from the eight official documents is in `docs/data/regulations/` (draft until Tim Validasi review, D-113). The PDFs are now in `docs/sources/` under descriptive names (D-203). Owner decisions: D-204…D-209. The `docs/final/` updates in §10 of `docs/drafts/dokumen-resmi-sd-mi.md` were applied in P4-03b.
- 2026-11-07 — P5-03b done (PR #55): sign-in, reset, invitations (staff invite the first operator/principal, the operator invites teachers), Akun, role switch, and console pages for memberships and supervisor assignments. D-182…D-184. Supervisor accounts come with P5-09 (D-184); server mail is set. Next: P5-03c, then P5-03d.
- 2026-11-04 — P4-02 in review: `docs/drafts/ifp-classroom.md` maps IFP use on papaninteraktif.com and game.digitaleducation.id and designs Game kelas as an RPM-driven IFP mode. Owner interview done: D-171…D-181. Open: Android/WebView version on a real Kemendikdasmen IFP, Kode Tayang hostname; final docs still list the old D-014 game list (update when the draft moves to final).
- 2026-11-03 — P5-03f in review: school directory for Kudus, Jepara, and Pati, with the Sebaran sekolah list, map, and Excel download (D-169, D-170). After deploy, run `php artisan school-directory:import` for each file in `docs/data/schools/`. Next: P5-03b in a new session.
- 2026-11-03 — P5-03a done (PR #52): schools, memberships, school scope, and staff console. D-167 staff roles in the console, D-168 supervisor assignments are not school-scoped.
- 2026-11-02 — D-166: local development on Windows runs in WSL2 (Ubuntu 24.04), not Laragon. Setup guide: `docs/drafts/local-setup-wsl2.md` (draft, needs owner approval before it moves to `docs/final/`).
- 2026-11-02 — D-165: app time is WIB (`Asia/Jakarta`), superseding D-082. The MySQL session uses `+07:00`; the server OS and PHP CLI stay UTC. The final docs and their PDFs are updated.
- 2026-11-01 — P5-01 done: Laravel app at the repo root with CI (see the task file's Result). Ziggy replaces Wayfinder (D-163); hostnames come from `APP_HOST`/`EXAM_HOST`/`ADMIN_HOST` (D-164), so the server `.env` needs them. P5-03 can start.

