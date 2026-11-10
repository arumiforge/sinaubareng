# Curriculum data

Curriculum source files for `php artisan curriculum:import` (loaded in the order of `config/curriculum.php`). The data model is in [`docs/final/data-model.md`](../../final/data-model.md) (Curriculum).

| File | Version code | What it holds |
|---|---|---|
| [`subjects.json`](subjects.json) | — | Subject catalogue: codes, names, aliases (D-227) |
| [`cp-pendidikan-agama-sd-bkpdm-020-2026.json`](cp-pendidikan-agama-sd-bkpdm-020-2026.json) | `CP-PABP-BKPDM-020-2026` | CP for the six SD religion subjects |
| [`cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json`](cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json) | `CP-PAI-BA-MADRASAH-9941-2025` | CP for MI's Kemenag subjects |
| [`mi-allocation-kma-1503-2025.json`](mi-allocation-kma-1503-2025.json) | `ALOKASI-MI-KMA-1503-2025` | MI JP allocation |
| [`sigrak-sd-2026-r1-umum.json`](sigrak-sd-2026-r1-umum.json) | `SIGRAK-SD-2026-R1` | General SD subjects, PAI, muatan lokal, KKA (kokurikuler content, D-208): topics, TP, ATP, fokus, JP |
| [`sigrak-sd-2026-r1-agama.json`](sigrak-sd-2026-r1-agama.json) | `SIGRAK-SD-2026-R1` | The five other SD religions: topics, TP, ATP, fokus, JP |
| [`sigrak-mi-2026-r1.json`](sigrak-mi-2026-r1.json) | `SIGRAK-MI-2026-R1` | The five MI Kemenag subjects: topics, TP, ATP, fokus, JP |
| [`crosswalk-legacy-2026.json`](crosswalk-legacy-2026.json) | — | Team only, **never imported**: old codes → SIGRAK codes (D-365) |

The SD allocation comes from the official structure, `docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json` (`STRUKTUR-SD-PERMENDIKDASMEN-13-2025`, D-366).

## SIGRAK R1 dataset

One format for all topics (D-363): the data model's own shape (D-140), one version per level and year. Codes: topic `SIGRAK-SD-2026-R1-PP-K1-01`, TP `…-TP01`, fokus `…-FM01`, ATP `ATP-<subject>-K<grade>-BAB-<nn>`. Each subject has its semester JP targets in `allocations` (the topics are checked against them at import) and a `curation` block. Each topic may have `references`, `curation_notes`, and `extras` (art branch, science process skills, subject status, learning type, ATP details).

Provenance: converted on 2026-11-09 by [`scripts/curriculum/convert_legacy_topics.py`](../../../scripts/curriculum/convert_legacy_topics.py) from the legacy app curriculum data (general SD subjects and PAI) and the two earlier SIGRAK religion drafts (D-139…D-141, D-145, D-146). The input files were removed afterwards; they are in the git history before this change. During conversion the D-234 Tata Boga trim went into the data, and notes that named the dropped files or the legacy app's internals were reworded.

| File | Topics | TP | Fokus | sha256 |
|---|---|---|---|---|
| `sigrak-sd-2026-r1-umum.json` | 432 | 1,372 | 2,021 | `c5e096caa1b1f735cec7bf2e708437834fca9a80d90ffb72f6709ff69b56e6c3` |
| `sigrak-sd-2026-r1-agama.json` | 300 | 765 | 1,060 | `4e339ca4734fa0aeb9e59e4ead0b9cdb62e915c309c86339ef2902596cc391fe` |
| `sigrak-mi-2026-r1.json` | 224 | 558 | 988 | `46351a397be2d36fadbe61e61222ba2f9d3b9eefdedeb525119e204134d53ec0` |
| `crosswalk-legacy-2026.json` | 956 | 2,695 | 4,069 | `3bd23ab67bba5fde511fa77cef6c74b590dd21d92535bccaca82d3028ccbf4eb` |

## Curation per subject (D-364)

`curation.status` ∈ {`converted`, `in_review`, `curated`}, with `since`, `reviewed_by`, `reviewed_on`, and `note`. Only `curated` subjects are active for teachers (enforced from P5-04). The import keeps each subject's curation in the version's file meta.

| Status | Subjects |
|---|---|
| `converted` (not yet reviewed) | PP, BIN, MTK, BIG, PJOK, PAI, SB, BJW, SSD, IPAS, KKA, KTB |
| `in_review` (SIGRAK draft from the verified CP) | PAKR, PAKT, PAH, PAB, PAKH; QH, AA, FIK, SKI, BAR |
| `curated` | none yet |

Checklist before a subject becomes `curated`: CP matches the official decree; the TP cover the CP; ATP order and JP match Permendikdasmen 13/2025 (SD) or KMA 1503/2025 (MI); every fokus is linked to its TP; known issues closed; references recorded. Curation tasks: P4-29 (SD core), P4-30 (SD others), P4-31 (religions SD and MI).

## Crosswalk

`crosswalk-legacy-2026.json` maps every old topic, TP, and fokus code (legacy app IDs and aliases, earlier draft codes) to its SIGRAK code, plus `retired` entries for old IDs that were merged into a topic. It is for the team, to trace a question about old data. Nothing in the app reads it, and old codes never appear in topics (`topics.legacy_codes` was dropped, D-365). Source findings on the legacy data: [`docs/drafts/curriculum-source-check-2026.md`](../../drafts/curriculum-source-check-2026.md).
