# P4-04 — Class and school administration features for SD/MI (research + recommendations)

- **Phase:** P4
- **Status:** done
- **Owner:** p4/p4-04-administration-features
- **Depends on:** D-009, D-158, D-161
- **Can run in parallel with:** P4-03 (official documents to data), P5 tasks (docs only)

## Goal

Research every class administration and school administration feature an SD/MI needs (Kemendikdasmen and Kemenag rules, the 2025 curriculum documents, and comparable Indonesian school apps), and recommend which ones should become main SIGRAK features on top of what the app already has. The owner decides; decisions go in `DECISIONS.md` from D-210 up.

## Read first

- `docs/agent/CONTEXT.md`, `docs/agent/PROGRESS.md`
- `docs/final/product-concept.md`, `docs/final/data-model.md` (skim)
- `docs/drafts/buku-induk-dapodik.md` (P4-01, on hold), `docs/drafts/ifp-classroom.md` (P4-02)
- `docs/dokumen-resmi/` (SD/MI parts only; moved to `docs/sources/` by P4-03, D-203)
- `docs/agent/DECISIONS.md` where a recommendation touches an earlier decision

## Output

- `docs/drafts/administration-features.md`
- Owner choices in `docs/agent/DECISIONS.md` (D-210 and up)
- Follow-up `todo` tasks in `docs/agent/PROGRESS.md` for agreed features

## Done when

- [x] Catalogue of candidate features, grouped by user, with basis, mandatory/nice-to-have, reuse, effort, and sales value
- [x] Benchmark of comparable Indonesian school apps and their gaps
- [x] Short list of the strongest candidates with phase/slice fit
- [x] Numbered decision list answered by the owner and recorded
- [x] Follow-up tasks added to `PROGRESS.md`

## Result

Draft written 2026-11-07 from the official documents, current regulations (2025–2026), and a benchmark of government and commercial apps. The owner decided on 2026-11-08 (D-210…D-221): all new rules are applied; all 31 listed items answered (most accepted, KSP after v1, e-Rapor/RDM export right after slice 6, ekskul and Pramuka early in v1, student savings added, teacher attendance not needed, parent portal/SPP/MBG/library upcoming); every new feature starts as core, and the console decides core vs add-on later. Follow-ups P4-05…P4-11 are in `PROGRESS.md`.

Open: the owner's SK pembagian tugas lampiran and supervision instrument samples did not reach the repo (needed for P4-08); portfolio photo limits; numbers marked *secondary* in the draft's sources must be re-checked before they appear in product copy.
