# P4-24 — Final docs sync (P4-05, P4-14, P4-20, P4-24 combined)

- **Phase:** P4
- **Status:** in-progress
- **Owner:** p4/p4-final-docs-sync
- **Depends on:** P4-03b, P4-13 (D-252…D-328), P4-16…P4-18 (D-329…D-351), P4-21 (D-352…D-359), owner answers D-368…D-371
- **Can run in parallel with:** P4-22, P4-23, P5-03i/j and the D-355 build (other sessions; no shared files except the PROGRESS rows)

## Goal

Bring `docs/final/` (except `research-digest.md`) in line with every accepted decision not yet applied there, translate the changed lines into `docs/pdf/src/`, and rebuild the Indonesian PDFs. One task replaces the four doc-update tasks P4-05, P4-14, P4-20, and P4-24.

## Read first

- `CLAUDE.md`, `docs/agent/CONTEXT.md`, `docs/agent/WORKFLOW.md` (Docs pipeline), `scripts/docs-pdf/README.md`
- Source lists: `docs/final/developer-handbook.md` §12.1, `docs/drafts/legacy-upgrade-01.md` §4, `docs/drafts/policy-innovation-layers.md` §12–§13
- `docs/agent/DECISIONS.md`: D-210…D-221, D-252…D-328, D-329…D-351, D-352…D-372

## Output

- `docs/final/*.md` (not `research-digest.md`), `docs/pdf/src/*.md`, `docs/pdf/*.pdf`
- Drafts marked as applied: `legacy-upgrade-01.md`, `policy-innovation-layers.md`, `administration-features.md`, `teacher-supervision-instruments.md`, `assessment-landscape.md`, `lkpd-exploration.md`, `internal-roles.md`, `open-questions.md`
- This file

## Done when

- [ ] Every item below is applied or marked deferred with a reason
- [ ] Changed final docs have their translated lines updated and PDFs rebuilt
- [ ] Handbook §12.1 lists only differences that remain
- [ ] PR open to `main`

## Checklist

Status: **applied** (in the final doc named), **deferred** (reason given), **n/a**.

_Filled in on finish._

## Result

_Filled in on finish._
