# P2-06 — Coding standards

- **Phase:** P2
- **Status:** in-progress
- **Owner:** p2/p2-06-coding-standards
- **Depends on:** —
- **Can run in parallel with:** P1-08, P2-01…P2-05

## Goal

Write the coding standards every developer and agent follows when the build starts.

## Read first

- `CLAUDE.md`, `docs/agent/CONTEXT.md`, `docs/agent/WORKFLOW.md`
- `docs/drafts/data-model.md` (conventions, tenancy), `docs/drafts/ai-provider-engine.md` (privacy rules), `docs/drafts/design-system.md` (UI writing, terms)

## Output

- `docs/drafts/coding-standards.md` (use `docs/templates/DOC.md`): PHP/Laravel (PSR-12 via Pint, strict types, enums for fixed sets, Form Requests, Policies, no business logic in controllers), folder layout (`app/AI`, `app/Content`, …), naming, migrations (constraints, indexes, no soft deletes), tenancy scope and IDOR tests, Svelte + Inertia conventions, translations (`lang/id`, English keys), Indonesian exports, dates/numbers formatting, tests (what to test, fake AI provider), AI privacy rules for code, logging/secrets, commit and PR rules (no attribution), dependency pinning.
- A "Decisions to record" section for anything new (e.g. Pest vs PHPUnit, TypeScript or not).

## Done when

- [ ] Consistent with CLAUDE.md, CONTEXT.md, and the drafts
- [ ] Each rule is short and checkable; examples where useful

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-06-coding-standards`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
