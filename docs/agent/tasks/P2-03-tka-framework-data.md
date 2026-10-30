# P2-03 — TKA SD/MI framework as seed data

- **Phase:** P2
- **Status:** in-progress
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

- [ ] Both subjects complete, every value traceable to an official source
- [ ] JSON valid; sources and access dates recorded

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-03-tka-framework-data`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
