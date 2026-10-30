# Progress

Single source of truth for what is happening. Keep it short: move finished phases to `archive/` when they stop being useful.

Status values: `todo` · `in-progress` · `blocked` · `review` · `done`.
Owner = branch name or session label of whoever claimed the task.

## Current phase

**Phase 1 — Product concept docs** · drafts written, waiting for owner review.

## Open decisions blocking work

- D-022 AI engine technical choices (proposed, needs owner review)

## Phases

### Phase 0 — Repository conventions · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P0-01 | No-attribution rule | done | pw/amazing-hawking-dn7dfo | — | `CLAUDE.md`, `.claude/settings.json` |
| P0-02 | Language, stack, docs structure, agent workflow | done | pw/amazing-hawking-dn7dfo | P0-01 | `CLAUDE.md`, `docs/` |

### Phase 1 — Product concept docs · review

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P1-01 | Archive owner sources + owner interview | done | pw/eloquent-shannon-9iwufg | — | `docs/sources/`, D-007…D-021, D-023…D-037 |
| P1-02 | Product concept | review | pw/eloquent-shannon-9iwufg | P1-01 | `docs/drafts/product-concept.md` |
| P1-03 | Content engine (templates, wizard, games, TKA, export) | review | pw/eloquent-shannon-9iwufg | P1-01 | `docs/drafts/content-engine.md` |
| P1-04 | AI Provider Engine | review | pw/eloquent-shannon-9iwufg | P1-01 | `docs/drafts/ai-provider-engine.md`, D-022 |
| P1-05 | Data model | review | pw/eloquent-shannon-9iwufg | P1-02…P1-04 | `docs/drafts/data-model.md` |
| P1-06 | Design system and UI writing | review | pw/eloquent-shannon-9iwufg | P1-01 | `docs/drafts/design-system.md` |
| P1-07 | Religious education curriculum (SD religions, MI Kemenag) | review | pw/eloquent-shannon-9iwufg | P1-05 | `docs/drafts/religious-education.md`, `docs/data/curriculum/` |

After approval: move the drafts to `docs/final/` and generate the Indonesian PDFs (one task per doc, can run in parallel).

### Phase 2 — candidates (not yet tasks)

Architecture overview · server setup guide (incl. Docker + Gotenberg) · coding standards · export/localization guide (incl. `lang/id` from the paraphrase spreadsheet) · v1 release slices (see `product-concept.md#v1-scope`) · curriculum data fixes (Matematika K4 S2, Tata Boga target, TP↔fokus links) · TKA framework data (competency matrix for BI and Matematika as seed data) · religion data: verify the SD religion CP transcription, build TP/ATP/fokus/JP for five religions and five MI subjects, obtain the official KMA 1503/2025 (see `religious-education.md#data-work-phase-2`).

## Handoff notes

Short notes for the next agent (newest first, max ~10 lines; delete stale ones).

- 2026-10-30 — D-037: Kepercayaan is not in v1, MI Kemenag subjects are taught by guru mapel, and each religion's data is reviewed by a guru agama of that religion.
- 2026-10-30 — Religion curriculum added (D-034…D-036). SD has six religions from BKPDM 020/2026, transcribed from a scan and still unverified. MI uses the Kemenag subjects from SK Dirjen Pendis 9941/2025. Data is in `docs/data/curriculum/`.
- 2026-10-30 — Owner review round 2 is applied (D-030…D-033): bundled TKA figures + three.js cube stacks, partial PGK scoring, letterhead as an uploaded image (no Word-to-PDF, no merge), guru kelas vs guru mapel defaults, rendering libraries, no slide feature.
- 2026-10-30 — Owner review round 1 is applied (D-023…D-029): Gemini free tier only, no generic generator (Buat bebas removed, one job per content type), payment outside BOS, Mode Ujian off, TKA practice CBT in v1 on a separate exam site, Gotenberg + Laravel Excel, daily attendance.
- 2026-10-30 — The owner supplied scope (EDUNEXA notes + SIGRAK concept) and decisions were made in an interview (D-007…D-021). Five drafts are in `docs/drafts/`, and each ends with open questions. The next step is the owner's review. Do not start Phase 2 before the drafts are approved.
