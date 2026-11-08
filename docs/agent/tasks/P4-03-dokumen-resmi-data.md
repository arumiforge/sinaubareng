# P4-03 — Official documents to SD/MI data

- **Phase:** P4
- **Status:** done
- **Owner:** p4/p4-03-dokumen-resmi-data
- **Depends on:** —
- **Can run in parallel with:** P4-04 (reads the same PDFs; new decisions use only D-203…D-209)

## Goal

Turn the eight official documents in `docs/dokumen-resmi/` into structured JSON data for the SD/MI level only (SD, MI, Fase A–C), and list what the final docs need to change.

## Read first

- `docs/agent/CONTEXT.md`
- `docs/data/curriculum/` and `docs/data/tka/` (format conventions)
- `docs/final/product-concept.md`, `docs/final/content-engine.md`, `docs/final/religious-education.md` (for the comparison)
- The PDFs, received in `docs/dokumen-resmi/`, now in `docs/sources/` (input only, never edited)

## Output

- `docs/data/regulations/*.json` (one file per document, `status: draft`, source metadata with page references)
- `docs/data/regulations/README.md`
- `docs/drafts/dokumen-resmi-sd-mi.md` (conflicts and updates the final docs need; proposal for `docs/sources/`)

## Done when

- [x] Each of the eight documents has an SD/MI JSON file with source metadata and page references
- [x] Scanned pages are checked against the page images
- [x] README lists the files
- [x] Draft note lists conflicts and updates for `docs/final/`
- [x] PDFs untouched

## Result

- **Data:** eight JSON files in `docs/data/regulations/` (35–150 KB each), all `status: draft`, `verified: false`, with SHA-256 and PDF page refs. `README.md` lists them. Text came from the PDF text layer. Tables, diagrams, and image-only or garbled pages were read from page images (listed per file in `extraction.checked_against_images`). One OCR error was corrected from the image (127/P/2025 p. 26). Source misprints are kept as printed and noted in `open_points`.
- **Comparison:** `docs/drafts/dokumen-resmi-sd-mi.md`.
  - The SD intrakurikuler JP in KM-SD-2026-R2 match Permendikdasmen 13/2025.
  - Conflicts: Bahasa Inggris grades 1–2, Tata Boga vs the muatan lokal cap, and SD kokurikuler JP missing from the data.
  - Proposed updates: eight profile dimensions as an enum; RPM INTI as Memahami/Mengaplikasi/Merefleksi; kokurikuler type "Cara lainnya" and the official plan schema; rapor minimum content (including ekstrakurikuler, which conflicts with D-161); Fase A without written tests.
  - KKA confirmed as an elective in grades 5–6, 2 JP, Fase C reference content only.
- **Decisions:** D-203 (data location; PDFs moved to `docs/sources/`), and D-204…D-209 from the owner's answers on 2026-11-08: optional Bahasa Inggris and IPAS in grades 1–2 (not required in the rapor) and Tata Boga, ekstrakurikuler and formative-based descriptions in the rapor, optional Fase A written tests, "Tidur Cepat", KKA through kokurikuler, KSP starter template.
- **Open:** owner approval of the `docs/final/` updates in §10 of the draft note (a follow-up task). `docs/final/` is untouched.
