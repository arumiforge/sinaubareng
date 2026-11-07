# P4-03 — Official documents to SD/MI data

- **Phase:** P4
- **Status:** in-progress
- **Owner:** p4/p4-03-dokumen-resmi-data
- **Depends on:** —
- **Can run in parallel with:** P4-04 (reads the same PDFs; new decisions use only D-203…D-209)

## Goal

Turn the eight official documents in `docs/dokumen-resmi/` into structured JSON data for the SD/MI level only (SD, MI, Fase A–C), and list what the final docs need to change.

## Read first

- `docs/agent/CONTEXT.md`
- `docs/data/curriculum/` and `docs/data/tka/` (format conventions)
- `docs/final/product-concept.md`, `docs/final/content-engine.md`, `docs/final/religious-education.md` (for the comparison)
- The PDFs in `docs/dokumen-resmi/` (input only, never edited)

## Output

- `docs/data/regulations/*.json` (one file per document, `status: draft`, source metadata with page references)
- `docs/data/regulations/README.md`
- `docs/drafts/dokumen-resmi-sd-mi.md` (conflicts and updates the final docs need; proposal for `docs/sources/`)

## Done when

- [ ] Each of the eight documents has an SD/MI JSON file with source metadata and page references
- [ ] Scanned pages are checked against the page images
- [ ] README lists the files
- [ ] Draft note lists conflicts and updates for `docs/final/`
- [ ] PDFs untouched

## Result

Filled in by the agent on finish.
