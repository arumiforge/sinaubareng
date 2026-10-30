# P2-02 — Structure the MI CP data and retype Arabic

- **Phase:** P2
- **Status:** in-progress
- **Owner:** p2/p2-02-mi-cp-structure
- **Depends on:** —
- **Can run in parallel with:** P1-08, P2-01, P2-03…P2-06

## Goal

Turn the raw MI CP text (Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab; Fase A–C) into structured element/sub-element rows like the SD religion data, and retype every Arabic passage correctly.

## Read first

- `docs/drafts/religious-education.md` (MI section)
- `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` (raw text; see its `extraction.note`)
- `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json` (target shape)
- `docs/sources/SK_Dirjen_Pendis_No_9941_Tahun_2025_CP_PAI_dan_Bahasa_Arab_pada_Madrasah.pdf` PDF pages 13–16, 44–48, 70–73, 90–92, 99–101 (has a text layer; Arabic must be read from the page images)

## Output

- `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` rewritten: per subject `elements` (with sub-elements, e.g. Pemahaman Konsep → Tajwid / Al-Qur'an / Hadis; Keterampilan Proses → Mengamati …), per phase `outcomes[] {element, sub_element, text}`; keep `raw_text` per phase for traceability; `extraction.structured: true`.
- Arabic passages retyped in Unicode with harakat, right-to-left order checked; list them in **Result**.

## Done when

- [ ] All five subjects, all MI phases (SKI: B–C only) structured
- [ ] Every Arabic passage retyped and checked against the page image
- [ ] JSON valid; no text lost compared with `raw_text`

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-02-mi-cp-structure`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
