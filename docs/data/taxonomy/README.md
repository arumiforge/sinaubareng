# SIGRAK master taxonomy and regulation registry (draft)

Seed data for the master taxonomy (D-357) and the regulation registry (D-354), task P4-22. Shape and reasons: [`docs/drafts/policy-innovation-layers.md`](../../drafts/policy-innovation-layers.md) §2.1, §6, §9. Every file is `status: draft`: Tim Validasi SIGRAK reviews it before any seeder reads it (D-113). Version: `TAX-2026-R1`.

## Files

| File | Facet | Holds | Nodes |
|---|---|---|---|
| `facet-a-policy.json` | A Kebijakan | Layers L0–L6, rule strengths (`force`, D-353), regulation kinds, the eight Standar Nasional Pendidikan | 41 |
| `facet-b-curriculum-structure.json` | B Struktur kurikulum | Level, phase, grade, semester, learning type, kokurikuler forms, subject kinds; links to subjects, CP, topics, TP, ATP, fokus, kokurikuler themes and activities, ekstrakurikuler | 43 |
| `facet-c-profile-character.json` | C Profil dan karakter | 8 DPL, Panca Cinta, KBC nilai dasar and levels, safe school culture; link to 7 KAIH | 35 |
| `facet-d-learning.json` | D Pembelajaran | Deep-learning principles, experiences, framework; practices, modes, media (open) | 34 |
| `facet-e-assessment.json` | E Asesmen | Core codes for Peta Asesmen: types, functions, purposes, domains, techniques, instruments, responses, KKTP, score processing, analysis, follow-up; link to assessment kinds | 78 |
| `facet-f-content.json` | F Konten | Content types, LKPD Wave 1 patterns, Game kelas templates; links to item types and difficulty | 36 |
| `facet-g-context.json` | G Konteks | Region levels, muatan lokal, local languages, Konteks Daerah kinds, regional calendar | 13 |
| `facet-h-administration.json` | H Administrasi | School roles, teacher duties, staff roles, people (murid), school documents, supervision instruments; link to Berkas Administrasi | 41 |
| `regulations.json` | — | `regulations` (38) and `regulation_relations` (28) | — |

Structure is checked by `tests/Feature/Data/TaxonomyDataTest.php` (Pest, runs in CI).

## Node shape

Each facet file has a `meta` block and `nodes`. A node follows §9.2:

| Key | Meaning |
|---|---|
| `code` | Stable code, uppercase ASCII, parts joined by `.` or `-` (`DPL.03`, `ASES-TUJUAN.DIAGNOSTIK`). Never reused or changed; labels may change |
| `facet` | `A`…`H`, the same as the file |
| `label_id` | Indonesian label people see |
| `parent` | Code of the parent node in the same facet, or `null` for a top-level list |
| `level_scope` | `sd`, `mi`, or `both` |
| `force` | Strength of the rule behind the concept, one of the `FORCE` keys in facet A (D-353). SIGRAK's own lists are `free` |
| `source_ref` | `{regulation, article, pages, note?, also?}` with a code from `regulations.json`, or `{source: "sigrak", decision}` |
| `status` | `draft` until Tim Validasi signs off |
| `since` / `retired_in` | Taxonomy version that added or retired the node |
| `crosswalk` | Older concepts this node replaces: `{from, type, note?}`, type `renamed`, `approximate`, `replaced`, `merged`, or `split` |

Optional keys: `vocabulary` (`closed` or `open`, required on every active top-level node), `key` (English snake_case value code reads), `members_from` (see below), `data_files`, `maps_to` and `links_to` (another node code), `note`, and a few facet-specific values (`grades`, `app_behaviour`, `category`, `feeds_report`, `sensitive`, `wave`, `build_status`, `ministry`).

## Rules

1. **Closed and open lists.** `closed` where a rule fixes the list (8 DPL, phases, principles); `open` where schools may add local items. A school item will be stored with `scope: school` and a code under the open list's code; it never changes the national list.
2. **Old terms are retired nodes.** Profil Pelajar Pancasila (`P3`, `P3.*`), `P5`, `P5RA`, `RPP`, `MODUL-AJAR`, and `PESERTA-DIDIK` are nodes with `retired_in: TAX-2026-R1`, kept only so older documents and guides can be read. A node whose `retired_in` equals its `since` was never offered for tagging. The current node points to them in `crosswalk`: P3 → 8 DPL, P5/P5RA → kokurikuler (`JENIS-PEMBELAJARAN.KOK`), RPP/Modul Ajar → RPM (`KONTEN.RPM`), peserta didik → murid (`PIHAK.MURID`). Two DPL links (`approximate`) are SIGRAK's reading, not an official mapping.
3. **Link, never copy.** Lists that already live in `docs/data/catalogs/` (D-367) or `docs/data/curriculum/` get one taxonomy node with `members_from: {files, path, code_field}`. Their items keep their own codes (`SIGRAK-KAT-KOK-KAIH-01`, `PP`, `SIGRAK-SD-2026-R1-PP-K1-01-TP01`); a tag stores the node code and the member code. Path syntax: `a[]` iterates a list, `a[key=value]` iterates a filtered list. Catalog lists without codes (`movement_forms_7kaih`, `outputs`, ekstrakurikuler `formats` and `days`) are not linked yet.
4. **Facet E is the core of Peta Asesmen.** P4-19 builds `docs/data/assessment/` (D-344) on these codes and does not redefine them: `ASES-TUJUAN.*` for `purposes.json`, `ASES-TEKNIK.*`, `ASES-INSTRUMEN.*`, `ASES-RESPONS.*` and `MEDIA.*` (facet D) for `methods.json`, `ASES-SKOR.*` for `scoring.json`, `ASES-TL.*` for `uses.json`, and `ASES-RANAH.*` as the top of `domains.json`. Subdomains and indicators are children with dotted codes (`ASES-RANAH.LIT.BACA`, then `ASES-RANAH.LIT.BACA.EVAL`). P4-19 may add new core codes here; it never renames one. `sensitive: true` domains follow D-350.
5. **New codes** take the parent's code plus a new part. A retired code keeps its node with `retired_in` set.

## Regulation registry

`regulations.json` follows §6.2. Each entry: `code`, `kind`, `number`, `title`, `issuer`, `ministry`, `issued_on`, `promulgated_on`, `effective_on`, `status` (`in_force`, `amended`, `revoked`, `draft`), `source_file` + `sha256`, `source_url`, `verification`, `notes`. Additions to §6.2:

- `layer` (`L0`…`L3`, §2), `year`, `data_files` (the extracts in `docs/data/` that read this source), `pending_p4_23` (true while the copy is being fetched by P4-23).
- `kind` adds `perka` (Peraturan Kepala badan) and `perdirjen` (Peraturan Dirjen) to the §6.2 list; the full list is `REGKIND` in facet A.
- `verification` adds `unverified` (no usable copy read yet), as in the research digest; `official_copy` = read in the official text, `mirror` = read on a mirror, `secondary` = news or summaries only.

Codes: `<KIND-OR-ISSUER>-<NUMBER>-<YEAR>` in uppercase, `/` and `.` become `-`: `PERMENDIKDASMEN-13-2025`, `PERMENDIKBUDRISTEK-12-2024`, `KEPMENDIKDASMEN-126-P-2025`, `KMA-512-2026`, `SK-DIRJEN-PENDIS-9941-2025`. Decrees of an agency head keep the short form already used in the curriculum and TKA data: `BKPDM-020-2026`, `BSKAP-046-H-KR-2025`, `PERKA-047-H-AN-2025`. Guides without a number keep the code of their extract (`PANDUAN-KOKURIKULER-BSKAP-2025`). The rule-set codes of `curriculum_versions` (`STRUKTUR-SD-PERMENDIKDASMEN-13-2025`) name a rule set, not a regulation.

`regulation_relations`: `{from, to, type, articles, note}` with type `amends`, `revokes`, `implements`, or `references`. Where the legal basis is not yet checked, the relation is `references` with a note.

Entries with `pending_p4_23: true` (Permendikbudristek 12/2024 and 21/2022, Permendikdasmen 1/2026, 10/2025, 12/2025, 26/2025, 6/2026, 9/2026, Kepmendikdasmen 17/2026, Kepka BSKAP 046/H/KR/2025) have `source_file` and `sha256` null. After the P4-23 PR merges, fill them, update `verification`, titles, and dates from the official copy, and set the flag to false. The test checks every non-null `sha256` against its file.

## Open points for Tim Validasi

- P3 → DPL: `P3.BERKEBINEKAAN-GLOBAL` → `DPL.02` and `P3.BERGOTONG-ROYONG` → `DPL.05` are marked `approximate`.
- DPL sub-dimensions are not seeded; no repo source lists them (check Permendikdasmen 10/2025 after P4-23).
- CP elements are free text in the curriculum files (`topics[].element`); element codes are still to be designed.
- Articles are missing where only a mirror or summary was read (`article: null`).
- Region codes (`WILAYAH`) need the official region list as data.
