# Progress

Single source of truth for what is happening. Keep it short: move finished phases to `archive/` when they stop being useful.

Status values: `todo` · `in-progress` · `blocked` · `review` · `done`.
Owner = branch name or session label of whoever claimed the task.

## Current phase

**Phases 0–3 are done** (2026-11-01). All approved docs are in `docs/final/` with Indonesian PDFs in `docs/pdf/`. Decisions run D-001…D-147. No open PRs, no running tasks, no worker branches in use. The next phase is not planned yet; the owner picks it.

## Open decisions blocking work

- None.

## Phases

### Phase 0 — Repository conventions · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P0-01 | No-attribution rule | done | pw/amazing-hawking-dn7dfo | — | `CLAUDE.md`, `.claude/settings.json` |
| P0-02 | Language, stack, docs structure, agent workflow | done | pw/amazing-hawking-dn7dfo | P0-01 | `CLAUDE.md`, `docs/` |

### Phase 1 — Product concept docs · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P1-01 | Archive owner sources + owner interview | done | pw/eloquent-shannon-9iwufg | — | `docs/sources/`, D-007…D-021, D-023…D-037 |
| P1-02 | Product concept | done | pw/eloquent-shannon-9iwufg | P1-01 | `docs/final/product-concept.md` |
| P1-03 | Content engine (templates, wizard, games, TKA, export) | done | pw/eloquent-shannon-9iwufg | P1-01 | `docs/final/content-engine.md` |
| P1-04 | AI Provider Engine | done | pw/eloquent-shannon-9iwufg | P1-01 | `docs/final/ai-provider-engine.md`, D-022 |
| P1-05 | Data model | done | pw/eloquent-shannon-9iwufg | P1-02…P1-04 | `docs/final/data-model.md` |
| P1-06 | Design system and UI writing | done | pw/eloquent-shannon-9iwufg | P1-01 | `docs/final/design-system.md` |
| P1-07 | Religious education curriculum (SD religions, MI Kemenag) | done | pw/eloquent-shannon-9iwufg | P1-05 | `docs/final/religious-education.md`, `docs/data/curriculum/` |
| P1-08 | Finish KMA 1503/2025 (MI allocation) + collect open questions | done | p1/p1-08-mi-allocation | P1-07 | `docs/data/curriculum/mi-allocation-kma-1503-2025.json`, `docs/drafts/open-questions.md`, D-038 |

Approved 2026-10-31 (D-123); moved to `docs/final/` in P3-01.

### Phase 2 — data and technical docs · done

Ran in parallel sessions and was integrated on `p2/p2-00-integration`. Decisions from the task outputs are D-039…D-085.

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P2-01 | Verify SD religion CP transcription | done | p2/p2-01-verify-religion-cp | — | `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json` |
| P2-02 | Structure MI CP data, retype Arabic | done | p2/p2-02-mi-cp-structure | — | `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` |
| P2-03 | TKA SD/MI framework seed data | done | p2/p2-03-tka-framework-data | — | `docs/data/tka/kerangka-tka-sd.json` |
| P2-04 | Architecture overview | done | p2/p2-04-architecture | — | `docs/final/architecture.md` |
| P2-05 | Server setup guide | done | p2/p2-05-server-setup | — | `docs/final/server-setup.md` |
| P2-06 | Coding standards | done | p2/p2-06-coding-standards | — | `docs/final/coding-standards.md` |
| P2-07 | Export and localization guide | done | p2/p2-07-export-localization | P2-06 | `docs/final/export-localization.md` |
| P2-08 | Server monitoring and alerts | done | p2/p2-08-server-monitoring | P2-05 | `docs/drafts/server-monitoring.md` |
| P2-09 | TP and ATP for five SD religion subjects | review | p2/p2-01-verify-religion-cp | P2-01 | `docs/data/curriculum/tp-atp-pendidikan-agama-sd-2026.json` |
| P2-10 | TP and ATP for five MI Kemenag subjects | review | p2/p2-01-verify-religion-cp | P2-02, P2-09 | `docs/data/curriculum/tp-atp-pai-bahasa-arab-mi-2026.json` |

### Phase 3 — final docs · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P3-01 | Move approved drafts to final + Indonesian PDFs | done | p3/p3-01-final-docs-pdf | D-123 | `docs/final/*.md`, `docs/pdf/*.pdf` |

Later candidates (not yet tasks): v1 release slices · curriculum data fixes (Matematika K4 S2, Tata Boga target, TP↔fokus links) · TP/ATP/fokus/JP for five religions and five MI subjects (reviewed by Tim Validasi SIGRAK, D-113) · import support for `ALOKASI-MI-KMA-1503-2025`.

## Start here (next session)

1. Read `CLAUDE.md`, `docs/agent/CONTEXT.md`, and this file. Stack, conventions, and every owner choice are in CONTEXT and `DECISIONS.md`.
2. Approved specs: `docs/final/*.md`. Still-living drafts: `docs/drafts/open-questions.md` (remaining owner questions) and `docs/drafts/server-monitoring.md` (P2-08, accepted by D-143 but not yet moved to final).
3. Data: `docs/data/curriculum/` (CP, MI allocation, TP/ATP drafts for SD religions and MI) and `docs/data/tka/`.
4. Old branches (`p1/*`, `p2/*`, `p3/*`, `pw/*`) are all merged. Do not reuse them; start new branches from `main`.

## Remaining work (not yet tasks)

- **Owner:** Tim Validasi SIGRAK reviews the CP and TP/ATP data before import (`status: draft`, D-113).
- **Docs:** rebuild `docs/pdf/server-setup.pdf` (it predates the D-136 backup heartbeat). Move `server-monitoring.md` to final and add its PDF. A committed PDF build script would make rebuilds repeatable.
- **Later candidates:** listed under Phase 2 above, plus a dedicated e-Rapor export (D-144) and v1 release slices before coding starts.

## Handoff notes

Short notes for the next agent (newest first, max ~10 lines; delete stale ones). Older notes: `archive/handoff-notes-2026-10-30-to-11-01.md`.

- 2026-11-01 — D-147: D-145/D-146 accepted. No decisions are open.
- 2026-11-01 — Coordination run closed. P0–P3 are merged (PRs #2–#43). All worker sessions are idle and can be archived.
