# P2-04 — Architecture overview

- **Phase:** P2
- **Status:** in-progress
- **Owner:** p2/p2-04-architecture
- **Depends on:** —
- **Can run in parallel with:** P1-08, P2-01…P2-03, P2-05, P2-06

## Goal

Write one architecture overview that ties the Phase 1 drafts together for developers.

## Read first

- `docs/agent/CONTEXT.md`, `docs/agent/DECISIONS.md` (read only)
- `docs/drafts/product-concept.md`, `content-engine.md`, `ai-provider-engine.md`, `data-model.md`, `religious-education.md`

## Output

- `docs/drafts/architecture.md` (use `docs/templates/DOC.md`): components (school app on Inertia+Svelte, separate exam site, Filament staff console, Redis, queues + Horizon, MySQL 8, Gotenberg, Gemini), module boundaries (`app/AI`, `app/Content`, tenancy, exam), the main flows (generation, export, offline sync, exam session), deployment topology on one Ubuntu VPS behind Cloudflare, security boundaries (guards, tenancy scope, key handling, exam isolation), and a diagram in Mermaid or ASCII.
- A "Decisions to record" section for anything new.

## Done when

- [ ] Every component and flow in the drafts appears once, consistent with the decisions
- [ ] Open questions listed

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-04-architecture`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
