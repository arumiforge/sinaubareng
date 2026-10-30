# Decisions

Append-only log. One entry per decision; newest at the bottom. Change a decision by adding a new entry that supersedes the old one.

Format: `D-NNN — Title` · Status (accepted / open / superseded by D-NNN) · Date · Decision · Reason.

---

### D-001 — No AI attribution
Accepted · 2026-10-30
No Claude/AI attribution in commits, PRs, GitHub comments, code, or docs. Enforced by `CLAUDE.md` and `.claude/settings.json`.
Reason: owner's requirement.

### D-002 — Language policy
Accepted · 2026-10-30
Repo markdown and code naming in English. UI text, app exports (Excel/CSV/Word/PDF), and generated PDFs in Indonesian.
Reason: owner's requirement; English keeps code/docs standard, Indonesian serves end users and readers.

### D-003 — Core stack
Accepted · 2026-10-30
Laravel, Nginx, PHP-FPM, Redis, multiple queue workers, Node.js (tooling), Python when needed, Cloudflare, Ubuntu Server 22.04 VPS, Tailwind + TailAdmin, Inertia.js, Filament, Telescope.
Reason: owner's requirement.

### D-004 — Database engine
**Open** · 2026-10-30
MySQL vs MariaDB not chosen yet. Until decided, keep migrations and queries portable.

### D-005 — Svelte vs SvelteKit with Inertia
**Open** · 2026-10-30
Owner mentioned "Inertia.js SvelteKit". Inertia works with plain **Svelte** (`@inertiajs/svelte`) — Laravel handles routing, so SvelteKit's router/server is not used. SvelteKit would only fit as a separate frontend talking to a Laravel API (no Inertia).
Recommendation: Inertia.js + Svelte. Awaiting owner confirmation.

### D-006 — Docs pipeline and parallel work
Accepted · 2026-10-30
Docs flow `docs/drafts/` → `docs/final/` → `docs/pdf/` (Indonesian). Work is phased; tasks run in parallel via sub-agents or sessions, coordinated through `PROGRESS.md` and `WORKFLOW.md`.
Reason: owner's requirement for staged, parallel, consistent, token-efficient work.
