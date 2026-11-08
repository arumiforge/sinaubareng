# Curriculum data

Curriculum source files for `php artisan curriculum:import`. The data model is in [`docs/final/data-model.md`](../../final/data-model.md) (Curriculum).

| File | Version code | What it holds |
|---|---|---|
| [`data_kurikulum_dengan_cp.json`](data_kurikulum_dengan_cp.json) | `KM-SD-2026-R2` | EDUNEXA national SD topics with CP, TP, ATP, fokus, and JP (incl. PAI). One array item = one topic |
| [`struktur_alokasi_sd.json`](struktur_alokasi_sd.json) | `KM-SD-2026-R2` | EDUNEXA SD JP allocation (`alokasi[]`, `kokurikuler_default[]`), generator rules, notes |
| [`cp-pendidikan-agama-sd-bkpdm-020-2026.json`](cp-pendidikan-agama-sd-bkpdm-020-2026.json) | `CP-PABP-BKPDM-020-2026` | CP for the six SD religion subjects |
| [`cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json`](cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json) | `CP-PAI-BA-MADRASAH-9941-2025` | CP for MI's Kemenag subjects |
| [`mi-allocation-kma-1503-2025.json`](mi-allocation-kma-1503-2025.json) | `ALOKASI-MI-KMA-1503-2025` | MI JP allocation |
| [`tp-atp-pendidikan-agama-sd-2026.json`](tp-atp-pendidikan-agama-sd-2026.json) | `TPATP-PABP-SD-2026-R1` | Draft TP/ATP for five non-Islam SD religions |
| [`tp-atp-pai-bahasa-arab-mi-2026.json`](tp-atp-pai-bahasa-arab-mi-2026.json) | `TPATP-PAI-BA-MI-2026-R1` | Draft TP/ATP for five MI subjects |

## KM-SD-2026-R2 provenance

The two EDUNEXA files are kept **exactly as received**: not reformatted, translated, or filtered. Do not edit them by hand. `curriculum:import` applies SIGRAK's rules (D-204, D-208) when it reads them. A newer download replaces the file whole and updates this table.

| File | Source URL | Downloaded | Size (bytes) | SHA-256 |
|---|---|---|---|---|
| `data_kurikulum_dengan_cp.json` | https://portal-edunexa.web.app/data_kurikulum_dengan_cp.json | 2026-11-08 | 2,354,903 | `158fe1d5ac7bb094552aacdb0638e1d4ca6d151ba1bd0f1257f632c706486524` |
| `struktur_alokasi_sd.json` | https://portal-edunexa.web.app/struktur_alokasi_sd.json | 2026-11-08 | 67,123 | `dd786cbe9db5dbe501931ecda25e4c9ffda83686d1c9798a009b56b0d2ec15f7` |

- Source: the owner's EDUNEXA portal (owner-run), served as `application/json`, HTTP 200 (task P5-03g).
- Both files are UTF-8 with LF line endings. `data_kurikulum_dengan_cp.json` has no final newline; that is how it was received.
- Check a copy: `sha256sum docs/data/curriculum/data_kurikulum_dengan_cp.json docs/data/curriculum/struktur_alokasi_sd.json`.
- Contents checked against the data model (432 topics, 2,021 fokus, 1,372 TP, 58 allocation rows): [`docs/drafts/km-sd-2026-r2-source-check.md`](../../drafts/km-sd-2026-r2-source-check.md).
