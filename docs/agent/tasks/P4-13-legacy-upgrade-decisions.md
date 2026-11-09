# P4-13 — Legacy app upgrade decisions (usulan-01 + Part B)

- **Phase:** P4
- **Status:** review
- **Owner:** claude/review-proposal-coverage-tzqbk4
- **Depends on:** D-251
- **Can run in parallel with:** P5 slices (docs only)

## Goal

Check that the two owner proposals of 2026-11-09 (usulan-01 and the Part B overlap draft) cover the legacy app walk-through session, settle their conflicts and gaps with the owner, and record the result as decisions. No app code.

## Read first

- `docs/sources/usulan-01/usulan-01-peta-fitur-aplikasi-lama-ke-sigrak.md`, `docs/sources/usulan-01/data/README.md`
- `docs/drafts/legacy-overlap-adjustments.md`
- `docs/drafts/legacy-coordination.md`

## Output

- `docs/sources/usulan-01/` (owner source, kept as received) and a row in `docs/sources/README.md`
- `docs/drafts/legacy-overlap-adjustments.md` (Part B, as received)
- `docs/drafts/legacy-coordination.md` (conflicts C-1…C-9, gaps G-1…G-24, sessions S1…S6, outcome)
- `docs/drafts/legacy-upgrade-01.md` (item → decision map, doc changes, tasks)
- `docs/agent/DECISIONS.md` D-252…D-328; status notes on D-016, D-023, D-031, D-117, D-153, D-174
- `docs/sources/Pedoman_Kalender_Pendidikan_Prov_Jawa_Tengah_2026_2027.pdf` (owner-supplied provincial kaldik)
- `docs/sources/Instrumen_Supervisi_Kudus_Pra_Observasi_Pasca.docx` (owner-supplied Kudus supervision set)
- `docs/agent/PROGRESS.md`, `tasks/P5-03-foundation.md` (P5-03i, P5-03j)

## Done when

- [x] Every U, B, T, Q, W item and every walk-through gap has a decision, a deferral, or a rejection
- [x] Each parallel session's answers checked against its transcript
- [ ] Owner approves the PR

## Result

- The walk-through session raised 24 ideas missing from both proposals. The two proposals conflicted in 9 places. Six parallel sessions (S1 to S6) interviewed the owner on 2026-11-09, 26 rounds in all. Each session's decisions were checked against the AskUserQuestion answers in its transcript.
- The coordinator asked a final round of four questions:
  - permission to apply stage 1 only;
  - Berkas: kept 7 days, free date range for teachers too, only the principal revokes an approval;
  - the attendance warning counts only "tanpa keterangan";
  - retention floors for rapor, leger, graduation data, buku induk, and supervision results.
- Result: 71 decisions, D-252…D-322. Duplicate class-event decisions from S1 and S2 were merged into D-258. Part B Q5 is replaced by D-286 (Pindai LJK is core in P5-08).
- Follow-ups:
  - P4-14: update `docs/final/` and the PDFs.
  - P4-15: catalog import.
  - P5-03i and P5-03j.
  - The slice additions listed in `legacy-upgrade-01.md` §5.
- Later on 2026-11-09 the owner sent the Jawa Tengah kaldik and a photo of a school's answer sheet, and asked about AI providers. That gave D-323…D-326:
  - one provincial reference calendar, 6-day version, Maulid on 25 August;
  - PKKS from the rules now, with regional variants later;
  - the LJK layout follows the schools' own sheet;
  - console adapters for OpenAI-compatible APIs and Anthropic, with teachers staying on Gemini.
  The photo shows a real student's name and score, so it was not stored.
- The owner then sent the Kudus supervision set: D-327 (regional variant, source scales, SIGRAK terms) and D-328 (manual score and optional evidence for principal items SIGRAK has no data for).
- Open data: the Kudus binder order. Tim Validasi checks the two calendar points listed in `legacy-upgrade-01.md`.
