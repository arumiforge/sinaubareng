# Progress

Single source of truth for what is happening. Keep it short: move finished phases to `archive/` when they stop being useful.

Status values: `todo` · `in-progress` · `blocked` · `review` · `done`.
Owner = branch name or session label of whoever claimed the task.

## Current phase

**Phase 1 — Product concept docs** · drafts in review (merged to `main`, still `review`).
**Phase 2 — data and technical docs** · running in parallel sessions (one task per session, see table).

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
| P1-08 | Finish KMA 1503/2025 (MI allocation) + collect open questions | review | p1/p1-08-mi-allocation | P1-07 | `docs/data/curriculum/mi-allocation-kma-1503-2025.json`, `docs/drafts/open-questions.md`, D-038 |

After approval: move the drafts to `docs/final/` and generate the Indonesian PDFs (one task per doc, can run in parallel).

### Phase 2 — data and technical docs · in-progress

Tasks run in parallel sessions. Each session edits only its own row here; see the coordination rules in each task file.

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P2-01 | Verify SD religion CP transcription | review | p2/p2-01-verify-religion-cp | — | `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json` |
| P2-02 | Structure MI CP data, retype Arabic | in-progress | p2/p2-02-mi-cp-structure | — | `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` |
| P2-03 | TKA SD/MI framework seed data | in-progress | p2/p2-03-tka-framework-data | — | `docs/data/tka/kerangka-tka-sd.json` |
| P2-04 | Architecture overview | in-progress | p2/p2-04-architecture | — | `docs/drafts/architecture.md` |
| P2-05 | Server setup guide | in-progress | p2/p2-05-server-setup | — | `docs/drafts/server-setup.md` |
| P2-06 | Coding standards | in-progress | p2/p2-06-coding-standards | — | `docs/drafts/coding-standards.md` |

Later candidates (not yet tasks): export/localization guide (incl. `lang/id` from the paraphrase spreadsheet) · v1 release slices · curriculum data fixes (Matematika K4 S2, Tata Boga target, TP↔fokus links) · TP/ATP/fokus/JP for five religions and five MI subjects (needs P2-01, P2-02 and guru agama reviewers, D-037) · move approved drafts to `docs/final/` + Indonesian PDFs.

## Handoff notes

Short notes for the next agent (newest first, max ~10 lines; delete stale ones).

- 2026-10-30 — Work is split into parallel sessions: P1-08 and P2-01…P2-06, one per branch, each with a task file. When they finish, an integration pass updates statuses here and numbers each task's "Decisions to record" in DECISIONS.md (D-038 is reserved for P1-08). KMA 1503/2025 is archived in `docs/sources/`, and its MI tables match the drafts.
- 2026-10-30 — D-037: Kepercayaan is not in v1, MI Kemenag subjects are taught by guru mapel, and each religion's data is reviewed by a guru agama of that religion.
- 2026-10-30 — Religion curriculum added (D-034…D-036). SD has six religions from BKPDM 020/2026, transcribed from a scan and still unverified. MI uses the Kemenag subjects from SK Dirjen Pendis 9941/2025. Data is in `docs/data/curriculum/`.
- 2026-10-30 — Owner review round 2 is applied (D-030…D-033): bundled TKA figures + three.js cube stacks, partial PGK scoring, letterhead as an uploaded image (no Word-to-PDF, no merge), guru kelas vs guru mapel defaults, rendering libraries, no slide feature.
- 2026-10-30 — Owner review round 1 is applied (D-023…D-029): Gemini free tier only, no generic generator (Buat bebas removed, one job per content type), payment outside BOS, Mode Ujian off, TKA practice CBT in v1 on a separate exam site, Gotenberg + Laravel Excel, daily attendance.
- 2026-10-30 — The owner supplied scope (EDUNEXA notes + SIGRAK concept) and decisions were made in an interview (D-007…D-021). Five drafts are in `docs/drafts/`, and each ends with open questions. The next step is the owner's review. Do not start Phase 2 before the drafts are approved.
