# P4-28 — SIGRAK curriculum dataset (one format, new codes) and SIGRAK catalogs

- **Phase:** P4
- **Status:** in-progress (data converted; code, docs, catalogs to do)
- **Owner:** sky/optimistic-thompson-2qmlfz
- **Depends on:** D-140, D-229, D-234, D-360
- **Can run in parallel with:** P4-22, P4-23

## Goal

The owner wants one curriculum view curated by SIGRAK, with nothing that looks like the legacy app: one data format, SIGRAK codes, curation per subject, and SIGRAK catalogs. Owner interview of 2026-11-09 chose: convert everything to SIGRAK's own format now and curate subject by subject (only curated subjects become active for teachers); keep old codes only in an internal crosswalk; name versions `SIGRAK-SD-2026-R1` / `SIGRAK-MI-2026-R1` (topic `SIGRAK-SD-2026-R1-PP-K1-01`, TP `…-TP01`, fokus `…-FM01`); scope: general SD topics, drop the legacy SD allocation file, recode the SD and MI religion drafts, curate the usulan-01 catalogs (P4-15).

## Decisions to record in `docs/agent/DECISIONS.md` (not yet written)

- **D-363 One SIGRAK curriculum format.** All topics/TP/ATP/fokus use the D-140 shape, one version per level and year (`SIGRAK-SD-2026-R1`, `SIGRAK-MI-2026-R1`), superseding "one version per CP set" of D-139/D-145 for topics. Official CP versions and structure versions stay separate. The importer reads only the SIGRAK format.
- **D-364 Curation per subject.** Each subject has `curation.status` ∈ {`converted`, `in_review`, `curated`} with reviewer and date; only `curated` subjects are active for teachers (enforced from P5-04). Checklist: CP matches the official decree, TP cover the CP, ATP order and JP match Permendikdasmen 13/2025 / KMA 1503, fokus linked to TP, known issues closed, references recorded.
- **D-365 Old codes only in a crosswalk.** `docs/data/curriculum/crosswalk-legacy-2026.json` (team only, never imported). `topics.legacy_codes` is dropped and `topics.audit` renamed `curation_notes` by a new forward-only migration (D-085).
- **D-366 SD allocation only from the official structure.** `struktur_alokasi_sd.json` is removed; semester JP targets live in the curated files (`subjects[].allocations`); weekly distribution stays a default until the calendar computes it (P5-03i). The D-234 Tata Boga trim is applied in the data and `SourceOverrides` is emptied (mechanism kept).
- **D-367 SIGRAK catalogs.** The usulan-01 catalogs are curated into `docs/data/catalogs/` with SIGRAK names and keys (`status: draft`, D-113); completes P4-15.
- History: earlier decision entries and archived drafts keep the old version code as history; active docs, code, and data use the new names.

## Done so far (2026-11-09)

- `scripts/curriculum/convert_legacy_topics.py` written and run. It produced `docs/data/curriculum/sigrak-sd-2026-r1-umum.json` (432 topics, 1,372 TP, 2,021 fokus; JP 7,534 = 7,540 − 6 for D-234), `sigrak-sd-2026-r1-agama.json` and `sigrak-mi-2026-r1.json` (524 topics together, counts unchanged), and `crosswalk-legacy-2026.json`. Topic keys: `references` (English keys), `curation_notes`, `extras` (art_branch, science_process_skills, subject_status, learning_type, atp leftovers); each subject has `curation`.

## Still to do

1. **Data:** delete `data_kurikulum_dengan_cp.json`, `struktur_alokasi_sd.json`, `tp-atp-pendidikan-agama-sd-2026.json`, `tp-atp-pai-bahasa-arab-mi-2026.json`. One `curation_notes` entry in the umum file lists old `CUR-…` IDs (Bahasa Indonesia grade 5); move those to the crosswalk and reword. Rewrite `docs/data/curriculum/README.md` (provenance: SIGRAK R1 dataset converted 2026-11-09, crosswalk, per-subject curation status, sha256). Add a short `scripts/curriculum/README.md`.
2. **Code:** delete `app/Curriculum/Import/Formats/KmSdTopics.php` and `KmSdAllocation.php` and their entries in `CurriculumImporter.php`. Extend `DataModelTopics.php`: read optional `extras` (merge with `sub_element`) and `curation_notes`, store each subject's `curation` in the file's meta, drop `legacy_codes`. `TopicInput`/`TopicWriter`/`SourceOverrides` (empty TOPICS list): `audit` → `curationNotes`, no `legacyCodes`. New migration `2026_11_10_000001_…`: drop `topics.legacy_codes`, rename `audit` → `curation_notes`; update `app/Models/Topic.php`. Remove old version names from comments in `AllocationWriter.php`, `TopicWriter.php`, `VersionRegistry.php`, `CurriculumVersion.php`, migrations 000001/000006. `config/curriculum.php` file list: subjects → CP files → SD structure 13/2025 → MI allocation → the three `sigrak-*` files. Tests: replace `tests/Fixtures/curriculum/km-topics.json`, `km-struktur.json`, adjust `topics-with-mismatches.json` and `tests/Feature/Curriculum/CurriculumImportTest.php` (old format rejected with a clear error; two files sharing a version merge; curation stored). Local PHP is 8.3, the project needs 8.5: run `php -l` locally, Pest runs in CI.
3. **Catalogs (P4-15):** from `docs/sources/usulan-01/data/` create `docs/data/catalogs/` (`cocurricular-themes.json`, `extracurriculars.json`, `class-inventory.json`, `form-options.json`, `admin-document-components.json`, `README.md`): English keys, Indonesian labels, codes `SIGRAK-KAT-…`, `status: draft`, `source_ref`; print/progress rules and holidays are not catalogs (point to D-280…D-283, D-263, and calendar task P5-03i). Follow D-207, D-216, D-277, D-305.
4. **Docs:** `docs/final/data-model.md` (version table, `topics` columns, Import paragraph), `developer-handbook.md`, `product-concept.md`, `religious-education.md`, `research-digest.md` and their `docs/pdf/src/` translations; rebuild those PDFs (`node scripts/docs-pdf/build.cjs …`, then `check.cjs`). Rename `docs/drafts/km-sd-2026-r2-source-check.md` → `curriculum-source-check-2026.md` and fix links. `CONTEXT.md`; `PROGRESS.md`: P4-28 and P4-15 done, add curation tasks P4-29 (SD core: PP, BIN, MTK, IPAS), P4-30 (SD others: SB, PJOK, BIG, BJW, KTB, SSD, KKA), P4-31 (religions SD + MI), each with the D-364 checklist and depending on P4-23 for the official general-subject CP; handoff note.
5. **Close:** commit as `Arumi Studios <arumiforge@gmail.com>` with no AI attribution, push, open a PR, watch CI.

## Done when

- [ ] `grep -rn "KM-SD\|Legacy_ID\|KmSd\|data_kurikulum_dengan_cp\|struktur_alokasi_sd\|TPATP-"` in `app/ config/ tests/ docs/final/ docs/pdf/src/ docs/data/` is empty except the crosswalk
- [ ] CI green; PDFs rebuilt; no AI attribution, no legacy brand
- [ ] Decisions D-363…D-367 recorded

## Result

Filled in on finish.
