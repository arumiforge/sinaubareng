# P2-03 — TKA SD/MI framework as seed data

- **Phase:** P2
- **Status:** review
- **Owner:** p2/p2-03-tka-framework-data
- **Depends on:** —
- **Can run in parallel with:** P1-08, P2-01, P2-02, P2-04…P2-06

## Goal

Capture the official TKA SD/MI assessment framework (Bahasa Indonesia, Matematika) as structured seed data for the Paket Soal TKA template and the TKA practice exam.

## Read first

- `docs/drafts/content-engine.md` (sections *Paket Soal TKA* and *TKA practice exam (CBT)*)
- Official sources (fetch them): https://pusmendik.kemendikdasmen.go.id/tka/tka/view/mata-pelajaran-wajib/sd/bahasa-indonesia and …/sd/matematika; Perka BSKAP No. 047/H/AN/2025 (Kerangka Asesmen TKA SD/MI dan SMP/MTs) — archive an official copy in `docs/sources/` if one can be downloaded, and add a README row

## Output

- `docs/data/tka/kerangka-tka-sd.json`: per subject — definition, content (BI: text types and text characteristics; Matematika: elements → sub-elements with scope and limits), competencies → sub-competencies, item forms (PG, PGK MCMA, PGK Kategori; soal tunggal/grup), duration (75 min) and item count (30), score range 0–100 and categories (Kurang < 50; Memadai 50–< 76,67; Baik 76,67–< 95; Istimewa ≥ 95), source URLs and access date.
- Do **not** copy the official sample questions; store metadata only (competency, form, key type) if useful.
- If facts differ from `content-engine.md`, list them under "Decisions to record" in **Result** (do not edit the draft).

## Done when

- [x] Both subjects complete, every value traceable to an official source
- [x] JSON valid; sources and access dates recorded

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-03-tka-framework-data`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

Filled in on 2026-10-30.

**Done**

- `docs/data/tka/kerangka-tka-sd.json`: both SD/MI subjects. Bahasa Indonesia has the definition, text types, text characteristics (with machine-readable limits: 3–7 words per sentence, 150–200 words per text except poems), the five reading aspects, and 3 competencies → 10 sub-competencies with stable keys. Matematika has the definition, content basis and contexts, 3 elements → 4 sub-elements → 23 scope points with keys and the *Batasan* limits, the five mathematical abilities, and 3 cognitive levels → 11 thinking processes. Shared parts: item types (tunggal/grup), the three item forms with key types, duration, item count, item origin, score range, categories and the Istimewa predicate. Every value has a `source` reference (regulation section and printed page, or URL); all sources have an access date.
- Official sample questions are not copied. Only their metadata is kept (competency, form, item type, option/statement counts, response labels), plus form counts of the 30-item sample sets on the Pusmendik pages.
- Archived in `docs/sources/` (README rows added): Perka BSKAP 047/H/AN/2025 and Kepmendikdasmen 56/2026, both from the Pusmendik download page.

**Source notes**

- The Pusmendik site sends an incomplete TLS chain and blocks non-browser clients; downloads worked with DigiCert's public intermediate added locally and a browser user agent. The per-subject pages render the same text as the Perka (checked).
- The item count (30) and the category limits (50; 76,67) are not in any regulation text found. They come from the official 30-item sample sets and from media reports of the Kapusmendik results briefing (26 May 2026). They are marked `confidence: secondary` and should be replaced when an official document states them. The scanned *Juknis TKA-AN 2026* and *Tanya Jawab TKA* PDFs may contain them but have no text layer and were not read (no OCR tool in the session).
- Perka sample BI no. 2 is labelled *PGK Kategori* but is built as PG (A–D, one key); the Pusmendik page labels it PG. Recorded as PG with the original label kept.

**Decisions to record** (facts that differ from `content-engine.md`; draft not edited)

1. **Categories (2026).** Kepmendikdasmen 56/2026 has three categories (Baik, Memadai, Kurang) and an **Istimewa predicate** for ≥ 95 on top of Baik (shown as "Baik-Istimewa"). The draft lists Istimewa as a fourth category and caps Baik at < 95. That was the 2025 rule (Kepmendikdasmen 95/M/2025). Proposal: show the category plus the predicate.
2. **Scoring model.** The draft says the official item-scoring model is not published. The ministry now says TKA uses classical scoring (0–100, two decimals) after item and package validation, and the briefing example (15 of 30 correct = 50) implies proportion correct. Partial credit for PGK is still not described, so D-030 partial credit stays a practice estimate, but the UI wording can be more specific.
3. **AN items merged into TKA (2026).** From 2026 the Bahasa Indonesia test includes AN literasi membaca items and Matematika includes AN numerasi items (Kepmendikdasmen 56/2026 BAB IV D). The official sample sets tag them with AKM literasi competencies (access and retrieve, interpret and integrate, evaluate and reflect) and narrower numeracy statements. Decide whether the Paket Soal TKA wizard offers these as extra competencies. The JSON lists them under `*_observed` only.
4. **Mathematics tagging.** The framework's tagging dimension is a sub-element scope point plus a **cognitive level** (Pengetahuan dan Pemahaman, Aplikasi, Penalaran) with named thinking processes. The draft's "memahami, mengaplikasikan, bernalar" matches the levels, but the levels should be the stored tag.
5. **PGK Kategori labels.** The decree names Benar/Salah and Sesuai/Tidak Sesuai as examples; official SMP samples also use Setuju/Tidak Setuju. Mendukung/Tidak Mendukung (in the draft) was not found in the SD sources. Proposal: allow Benar/Salah, Sesuai/Tidak Sesuai, Setuju/Tidak Setuju.
6. **Duration history.** 75 minutes is the 2026 value; 2025 used 60 minutes per subject. The draft value stays correct; keep it configurable.
7. **PGK MCMA in samples** have 3 statements with 2 correct, and the official 30-item sample sets have form mixes of BI 16 PG / 6 MCMA / 8 Kategori and Matematika 18 PG / 3 MCMA / 9 Kategori. These could be the default form mix of a 30-item package (not an official blueprint).

**Follow-ups**

- Find an official document for the item count and the 50 / 76,67 limits (OCR the scanned Juknis TKA-AN 2026 or Tanya Jawab TKA).
- The SMP/MTs part of the same Perka is archived and could be structured later if SMP is ever in scope.
