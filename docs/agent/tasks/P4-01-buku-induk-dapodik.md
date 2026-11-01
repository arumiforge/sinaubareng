# P4-01 — Buku Induk and Dapodik sync design

- **Phase:** P4
- **Status:** review
- **Owner:** main
- **Depends on:** D-157, D-158
- **Can run in parallel with:** —

## Goal

Design the Buku Induk add-on and the Dapodik sync: the operator uploads the Excel files exported from Dapodik, and SIGRAK recognizes, checks, matches, and applies the data, with a review step before anything changes.

## Read first

- `docs/final/data-model.md` (schools, students, enrollments, classes)
- `docs/final/export-localization.md` (XP-20…XP-25, D-150)
- `docs/final/architecture.md` (module boundaries, queues)

## Output

- `docs/drafts/buku-induk-dapodik.md`

## Done when

- [x] Import flow from upload to apply, with matching keys and conflict rules
- [x] Field list kept from Dapodik, and fields deliberately not stored
- [x] Data model additions, access rules, and checks
- [x] Open questions for the owner, including sample Dapodik files

## Result

2026-11-01: draft written in `docs/drafts/buku-induk-dapodik.md`. The Dapodik 2026 column layout is not published, so the design recognizes files by their headings, and the exact column map waits for anonymized sample exports from the owner (open question 1).
