# P2-05 — Server setup guide

- **Phase:** P2
- **Status:** in-progress
- **Owner:** p2/p2-05-server-setup
- **Depends on:** —
- **Can run in parallel with:** P1-08, P2-01…P2-04, P2-06

## Goal

Write a step-by-step guide to prepare the production VPS.

## Read first

- `docs/agent/CONTEXT.md`
- `docs/drafts/content-engine.md` (Export: Gotenberg), `docs/drafts/ai-provider-engine.md` (queues), `docs/drafts/product-concept.md` (exam site)

## Output

- `docs/drafts/server-setup.md` (use `docs/templates/DOC.md`): Ubuntu Server 22.04; Nginx + PHP-FPM (PHP version from the current Laravel release requirements, checked online); MySQL 8; Redis; Supervisor running Horizon and the scheduler; Docker with a **pinned** Gotenberg 8 image bound to localhost with basic auth; Node only for builds if possible; Cloudflare (DNS, proxy, real-IP restore in Nginx, TLS mode); separate exam subdomain vhost; UFW firewall; backups (MySQL dumps + storage) and restore test; log rotation. Exact commands in code blocks.
- A "Decisions to record" section (e.g. versions chosen).

## Done when

- [ ] Every component in CONTEXT.md covered, with versions verified online and the date checked
- [ ] Commands are copy-pasteable; secrets shown as placeholders only

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-05-server-setup`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
