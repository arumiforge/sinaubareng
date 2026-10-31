# P2-07 — Export and localization guide

- **Phase:** P2
- **Status:** in-progress
- **Owner:** p2/p2-07-export-localization
- **Depends on:** — (P2-06 coding standards is merged)
- **Can run in parallel with:** —

## Goal

Write one guide for how SIGRAK's Indonesian UI text and its Indonesian exports (PDF, DOCX, Excel, CSV) are built, so every screen and every export follows the same rules from the first line of code.

## Read first

- `CLAUDE.md` (language policy), `docs/agent/CONTEXT.md` (conventions)
- `docs/drafts/coding-standards.md` sections 7 (translations) and 8 (exports, dates, numbers)
- `docs/drafts/content-engine.md#export`, `docs/drafts/design-system.md` (UI writing, `#terms`)
- `docs/sources/Parafrase_Teks_UI_Sigrak.xlsx` (sheets *Parafrase*, *Istilah*, *Ringkasan*)
- `docs/agent/DECISIONS.md` only for D-002, D-028, D-031, D-033, D-080 (read only)

## Output

- `docs/drafts/export-localization.md` (use `docs/templates/DOC.md`), covering:
  - **`lang/id` layout:** one file per area (e.g. `auth`, `nav`, `rpm`, `exam`, `export`, `validation`), English dot keys, a naming scheme, and when to use parameters and `trans_choice`. Show how the shared Inertia prop and `t()` (D-080) load only the groups a page needs.
  - **From the paraphrase spreadsheet to keys:** how the 1,499 strings and 37 terms map onto files and keys. Include a mapping rule, a sample table (about 20 rows: old text → new text → key), how *Istilah* terms are kept consistent (one glossary file or a check), and how to find strings that are missing or unused.
  - **Validation and system messages:** Indonesian `validation.php` attributes and friendly error text. Mail and notification copy.
  - **Export rules per format:**
    - **PDF:** Gotenberg, one HTML view per PDF, letterhead image (D-028, D-031), A4/F4, fonts incl. Arabic, page numbers.
    - **DOCX:** PhpWord.
    - **Excel and CSV:** Laravel Excel. Headings via `__('export.columns.*')`, numbers stored as numbers, column formats for dates and Rupiah, CSV separator and encoding for Indonesian Excel (`;` + UTF-8 BOM, or not; decide).
  - **Formatting helpers:** dates (`d/m/Y`, long form "31 Oktober 2026"), numbers (`1.234.567,89`), `Rp`, `Asia/Jakarta` display with UTC storage. Name one helper (e.g. `IndonesianFormat`) and give its API.
  - **File names of exports:** Indonesian, slug rules, and no student personal data in file names.
  - **RTL/Arabic in UI and exports:** bidi isolation for MI CP text (D-039).
  - **Checks:** tests or CI checks that keep this true, e.g. no hard-coded UI strings, every key present in `lang/id`, and a snapshot of export headings.
- A "Decisions to record" section for anything new.

## Done when

- [ ] Every export format in `content-engine.md#export` and every translation rule in `coding-standards.md` (sections 7–8) is covered, with no contradiction
- [ ] The spreadsheet mapping rule and the sample table are in the guide, and the 37 *Istilah* terms have a stated home
- [ ] Rules are short and checkable, with code examples where useful; open questions listed

## Coordination rules

- Work on branch `p2/p2-07-export-localization`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Write handoff notes in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. List new decisions under "Decisions to record" in the guide; they get numbered at integration.
- Do not create `lang/id` files or app code yet; this is a guide. A sample of a few keys inside the guide is fine.

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
