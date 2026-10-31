# P2-09 — TP and ATP for five SD religion subjects

- **Phase:** P2
- **Status:** in-progress
- **Owner:** p2/p2-01-verify-religion-cp
- **Depends on:** P2-01 (verified CP)
- **Can run in parallel with:** other P2 tasks (touches only its own data file and this row)

## Goal

Draft topics (materi pokok/ATP), TP, fokus materi, and JP for Pendidikan Agama Kristen, Katolik, Hindu, Buddha, and Khonghucu dan Budi Pekerti, SD grades 1–6, semesters 1–2, from the verified CP, so the RPM wizard can offer them like PAI. Tim Validasi SIGRAK reviews the set afterwards (D-112, D-113).

## Read first

- `docs/drafts/religious-education.md` (SD section, Data work item 3)
- `docs/drafts/data-model.md` (Curriculum)
- `docs/sources/Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md` (KM-SD-2026-R2 shape and PAI allocation)
- `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json`

## Output

- `docs/data/curriculum/tp-atp-pendidikan-agama-sd-2026.json`

## Done when

- [ ] 5 topics per semester per grade for each religion (300 topics), each with TP and fokus
- [ ] Every CP element/sub-element of each phase covered; JP matches the PAI allocation (54/54, grade 6 54/42)
- [ ] Codes unique; fokus linked to TP; review status pending for Tim Validasi SIGRAK
- [ ] JSON valid; Result filled in

## Coordination rules (parallel sessions)

- Edit only this file, the output file, and this task's row in `PROGRESS.md`.
- Do not edit `DECISIONS.md` or `CONTEXT.md`; list decisions under "Decisions to record" in Result.

## Result

Filled in by the agent on finish.
