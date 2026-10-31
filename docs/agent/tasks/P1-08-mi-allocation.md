# P1-08 — Finish KMA 1503/2025 and Phase 1 open questions

- **Phase:** P1
- **Status:** done
- **Owner:** p1/p1-08-mi-allocation
- **Depends on:** P1-07
- **Can run in parallel with:** P2-01…P2-06

## Goal

Record the official MI subject allocation from KMA 1503/2025 as data, remove the remaining "to verify" wording about it, and collect every open question from the Phase 1 drafts into one list for the owner.

## Read first

- `docs/agent/CONTEXT.md`
- `docs/drafts/religious-education.md` (MI section)
- `docs/sources/KMA_No_1503_Tahun_2025_Pedoman_Implementasi_Kurikulum_Madrasah.pdf` pages 28–31 (scanned; read the page images)
- The *Open questions* section of every file in `docs/drafts/`

## Output

- `docs/data/curriculum/mi-allocation-kma-1503-2025.json`: every MI subject for grades I, II, III–IV, V, VI with `intrakurikuler`, `kokurikuler`, `total` JP per year, `weeks_per_year` (36; 32 for grade VI), `jp_minutes` (35), `required` vs `elective` (Koding dan KA, grades V–VI), and the table notes (choose one art; muatan lokal 72–216, 64–192 in grade VI; up to +6 JP/week). Record `source.pages` per table and `verified: true` with the method.
- `docs/drafts/religious-education.md` and `docs/drafts/data-model.md`: replace "must be verified" / "official copy not obtained" wording about KMA 1503/2025 with "verified against KMA 1503/2025 Lampiran II, Tabel 1–5 (pp. 28–31)" and link the JSON. Keep only "official-channel copy of SK 9941/2025" as an open source question.
- `docs/agent/DECISIONS.md`: append **D-038 — MI allocation confirmed from the official KMA 1503/2025** (closes the "official copy still needed" part of D-035).
- `docs/drafts/open-questions.md` (new): every unresolved open question from all drafts, grouped by topic (business, brand, content engine, AI, data model, design, religion/curriculum, TKA), each with a link to its source section and who must answer (owner / team). Leave the per-doc lists in place.

## Done when

- [x] JSON totals match the decree: intrakurikuler I 1,044 · II 1,116 · III–IV 1,368 · V 1,368 · VI 1,216; kokurikuler 108 (I–V), 96 (VI)
- [x] No "to verify" or "not obtained" wording about KMA 1503/2025 left in `docs/drafts/`
- [x] D-038 appended; `open-questions.md` complete

## Head start (already read from the scan; re-check every number)

| Subject | I | II | III–IV | V | VI (32 wk) |
|---|---|---|---|---|---|
| Al-Qur'an Hadis | 72+36 | 72+36 | 72 | 72 | 64 |
| Akidah Akhlak | 72 | 72 | 72 | 72+36 | 64+32 |
| Fikih | 72 | 72 | 72+36 | 72 | 64 |
| Sejarah Kebudayaan Islam | — | — | 72 | 72 | 64 |
| Bahasa Arab | 72 | 72 | 72 | 72 | 64 |
| Pendidikan Pancasila | 144+36 | 144+36 | 144 | 144 | 128 |
| Bahasa Indonesia | 252+36 | 288+36 | 216+36 | 216+36 | 192+32 |
| Matematika | 144 | 180 | 180 | 180 | 160 |
| IPAS | — | — | 180+36 | 180 | 160 |
| PJOK | 108 | 108 | 108 | 108 | 96+32 |
| Seni dan Budaya (pick one) | 108 | 108 | 108 | 108+36 | 96 |
| Bahasa Inggris | — | — | 72 | 72 | 64 |
| Koding dan KA (elective) | — | — | — | 72 | 64 |
| Muatan Lokal | 72–216 | 72–216 | 72–216 | 72–216 | 64–192 |

(`a+b` = intrakurikuler + kokurikuler JP per year.)
## Coordination rules (parallel sessions)

- Work on branch `p1/p1-08-mi-allocation`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md` — **exception: this task appends D-038**. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

- **Data:** `docs/data/curriculum/mi-allocation-kma-1503-2025.json` (`ALOKASI-MI-KMA-1503-2025`). Every cell of Tabel 1–5 (pp. 28–31) was read from the scan and matches the head-start table. Required totals equal the decree's printed rows (I 1,044 · II 1,116 · III–IV 1,368 · V 1,368 · VI 1,216; kokurikuler 108/96), and the ranges with muatan lokal and the elective equal the printed total rows. The implementation rules on pp. 31–32 (muatan lokal forms, Koding dan KA, +6 JP/week, kokurikuler team) are included.
- **Source quirk:** Tabel 5 labels the 1,280–1,408 row "Wajib + Pilihan + Muatan Lokal", but the figures are Wajib + Muatan Lokal only. Recorded in `source_quirks`; the dataset follows the figures.
- **Docs:** KMA 1503/2025 "to verify / not obtained" wording removed from `religious-education.md`, `data-model.md`, `product-concept.md` (open-question line) and `docs/sources/README.md`. D-038 appended.
- **Open questions:** `docs/drafts/open-questions.md` collects every open question from the six drafts plus inline ones (D-022 still proposed, Kabar notice, SD religion JP row, Laravel Excel on Laravel 13, v1 slices, known curriculum data issues).
- **Follow-ups:** import support for `ALOKASI-MI-KMA-1503-2025` in `curriculum:import`; an official-channel copy of SK 9941/2025 is still open.
- **Follow-up (2026-10-31):** archived SK Dirjen Pendis 6077/2025 (Panduan Kurikulum Berbasis Cinta) and summarised it in `religious-education.md`; owner question on KBC in the MI RPM template added to `open-questions.md`. Compared the owner's second copy of SK 9941/2025 with the archived file (identical text, missing only the Kanwil DKI cover page) and corrected `pages_note` in the MI CP JSON.
