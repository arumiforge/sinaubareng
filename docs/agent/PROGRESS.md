# Progress

Single source of truth for what is happening. Keep it short: move finished phases to `archive/` when they stop being useful.

Status values: `todo` · `in-progress` · `blocked` · `review` · `done`.
Owner = branch name or session label of whoever claimed the task.

## Current phase

**Phase 1 — Foundation docs** · waiting for product scope from the owner.

## Open decisions blocking work

- D-004 Database engine (MySQL vs MariaDB)
- D-005 Svelte vs SvelteKit with Inertia

## Phases

### Phase 0 — Repository conventions · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P0-01 | No-attribution rule | done | pw/amazing-hawking-dn7dfo | — | `CLAUDE.md`, `.claude/settings.json` |
| P0-02 | Language, stack, docs structure, agent workflow | done | pw/amazing-hawking-dn7dfo | P0-01 | `CLAUDE.md`, `docs/` |

### Phase 1 — Foundation docs · todo

Tasks are added once the owner provides product scope. Expected candidates (not yet tasks):
project overview, architecture overview, server setup guide, coding standards, export/localization guide.

## Handoff notes

Short notes for the next agent (newest first, max ~10 lines; delete stale ones).

- 2026-10-30 — Repo structure and rules in place. Next: get product scope from owner, then break Phase 1 into tasks in `tasks/`.
