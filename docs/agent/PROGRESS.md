# Progress

Single source of truth for what is happening. Keep it short: move finished phases to `archive/` when they stop being useful.

Status values: `todo` · `in-progress` · `blocked` · `review` · `done`.
Owner = branch name or session label of whoever claimed the task.

## Current phase

**Phases 0–3 are done** (2026-11-01). All approved docs are in `docs/final/` with Indonesian PDFs in `docs/pdf/`. Decisions run D-001…D-166. No open PRs, no running tasks. Only `main` exists on the remote (D-148). P4-01 (Buku Induk and Dapodik sync) is on hold as upcoming (D-161). **Phase 5 (v1 build) is next:** P5-01 and P5-02 can start now (D-162).

## Open decisions blocking work

- None.

## Phases

### Phase 0 — Repository conventions · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P0-01 | No-attribution rule | done | pw/amazing-hawking-dn7dfo | — | `CLAUDE.md`, `.claude/settings.json` |
| P0-02 | Language, stack, docs structure, agent workflow | done | pw/amazing-hawking-dn7dfo | P0-01 | `CLAUDE.md`, `docs/` |
| P0-03 | Owner commit identity, one `main` branch, Indonesian URLs | done | main | D-148, D-149 | `CLAUDE.md`, `.claude/`, `docs/agent/`, rewritten history |

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
| P2-08 | Server monitoring and alerts | done | p2/p2-08-server-monitoring | P2-05 | `docs/final/server-monitoring.md` |
| P2-09 | TP and ATP for five SD religion subjects | review | p2/p2-01-verify-religion-cp | P2-01 | `docs/data/curriculum/tp-atp-pendidikan-agama-sd-2026.json` |
| P2-10 | TP and ATP for five MI Kemenag subjects | review | p2/p2-01-verify-religion-cp | P2-02, P2-09 | `docs/data/curriculum/tp-atp-pai-bahasa-arab-mi-2026.json` |

### Phase 3 — final docs · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P3-01 | Move approved drafts to final + Indonesian PDFs | done | p3/p3-01-final-docs-pdf | D-123 | `docs/final/*.md`, `docs/pdf/*.pdf` |
| P3-02 | PDF build script + rebuild outdated PDFs | done | p3/p3-02-pdf-build-script | P3-01 | `scripts/docs-pdf/`, `docs/pdf/*.pdf` |
| P3-03 | Move server monitoring to final + Indonesian PDF | done | main | D-143 | `docs/final/server-monitoring.md`, `docs/pdf/server-monitoring.pdf`, `docs/pdf/server-setup.pdf` |

Later candidates (not yet tasks): curriculum data fixes (Matematika K4 S2, Tata Boga target, TP↔fokus links) · TP/ATP/fokus/JP for five religions and five MI subjects (reviewed by Tim Validasi SIGRAK, D-113) · import support for `ALOKASI-MI-KMA-1503-2025`.

### Phase 4 — add-on designs · on hold

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P4-01 | Buku Induk and Dapodik sync design | blocked (upcoming, D-161) | main | D-157, D-158 | `docs/drafts/buku-induk-dapodik.md` |

### Phase 5 — v1 build · todo

Order confirmed by D-162. P5-01 and P5-02 run in parallel. Each slice gets its task file, split into smaller tasks, when it starts.

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P5-01 | Project scaffold and CI | done | pw/jolly-fermi-c64iie | — | Laravel app at the repo root, `.github/workflows/` |
| P5-02 | Server, staging, and monitoring setup | todo | — | — | Running VPS (no repo output) |
| P5-03 | Slice 1 — Foundation: auth, invitations, schools and roles, academic year and classes, curriculum import, timetable | todo | — | P5-01 | App code |
| P5-04 | Slice 2 — Content core: AI provider engine, template engine, RPM, Dokumen Saya, PDF/DOCX export | todo | — | P5-03 | App code |
| P5-05 | Slice 3 — More templates: Bank Soal, LKPD, Quiz Battle | todo | — | P5-04 | App code |
| P5-06 | Slice 4 — TKA: package template, exam site, sessions, cards, token, results (target: 2028 season) | todo | — | P5-05 | App code |
| P5-07 | Slice 5 — Di Kelas with offline sync, Jurnal from RPM | todo | — | P5-03 | App code |
| P5-08 | Slice 6 — Asesmen: plan, scores, analysis per TP, report cards | todo | — | P5-05 | App code |
| P5-09 | Slice 7 — Portals: principal, operator, supervisor; validation and comments | todo | — | P5-03 | App code |
| P5-10 | Slice 8 — Business: trial, subscriptions, add-ons, manual payment, Mode Contoh | todo | — | P5-03 | App code |

## Start here (next session)

1. Read `CLAUDE.md`, `docs/agent/CONTEXT.md`, and this file. Stack, conventions, and every owner choice are in CONTEXT and `DECISIONS.md`.
2. Approved specs: `docs/final/*.md`. Still-living drafts: `docs/drafts/open-questions.md` (remaining owner questions), `docs/drafts/buku-induk-dapodik.md` (P4-01, on hold, D-161), and `docs/drafts/local-setup-wsl2.md` (local setup, D-166).
3. Data: `docs/data/curriculum/` (CP, MI allocation, TP/ATP drafts for SD religions and MI) and `docs/data/tka/`.
4. Only `main` exists. Old branches were deleted and the history was rewritten on 2026-11-01 (D-148), so old clones must be re-cloned or reset to `origin/main`. Start any task branch from `main` and delete it after merge.

## Remaining work (not yet tasks)

- **Owner:** Tim Validasi SIGRAK reviews the CP and TP/ATP data before import (`status: draft`, D-113).
- **Docs:** every doc in `docs/final/` has a matching Indonesian PDF, and every translation lives in `docs/pdf/src/` (D-156).
- **Data:** transcribe the special-education religion CP (Lampiran II of BKPDM 020/2026) for the six religions (D-155).
- **Later candidates:** listed under Phase 2 above, plus a dedicated e-Rapor export (D-144).

## Handoff notes

Short notes for the next agent (newest first, max ~10 lines; delete stale ones). Older notes: `archive/handoff-notes-2026-10-30-to-11-01.md`.

- 2026-11-02 — D-166: local development on Windows runs in WSL2 (Ubuntu 24.04), not Laragon. Setup guide: `docs/drafts/local-setup-wsl2.md` (draft, needs owner approval before it moves to `docs/final/`).
- 2026-11-02 — D-165: app time is WIB (`Asia/Jakarta`), superseding D-082. The MySQL session uses `+07:00`; the server OS and PHP CLI stay UTC. The final docs and their PDFs are updated.
- 2026-11-01 — P5-01 done: Laravel app at the repo root with CI (see the task file's Result). Ziggy replaces Wayfinder (D-163); hostnames come from `APP_HOST`/`EXAM_HOST`/`ADMIN_HOST` (D-164), so the server `.env` needs them. P5-03 can start.

- 2026-11-01 — D-147: D-145/D-146 accepted. No decisions are open.
- 2026-11-01 — Coordination run closed. P0–P3 are merged (PRs #2–#43). All worker sessions are idle and can be archived.
