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
Superseded by D-007 · 2026-10-30
MySQL vs MariaDB not chosen yet. Until decided, keep migrations and queries portable.

### D-005 — Svelte vs SvelteKit with Inertia
Superseded by D-008 · 2026-10-30
Owner mentioned "Inertia.js SvelteKit". Inertia works with plain **Svelte** (`@inertiajs/svelte`) — Laravel handles routing, so SvelteKit's router/server is not used. SvelteKit would only fit as a separate frontend talking to a Laravel API (no Inertia).
Recommendation: Inertia.js + Svelte. Awaiting owner confirmation.

### D-006 — Docs pipeline and parallel work
Accepted · 2026-10-30
Docs flow `docs/drafts/` → `docs/final/` → `docs/pdf/` (Indonesian). Work is phased; tasks run in parallel via sub-agents or sessions, coordinated through `PROGRESS.md` and `WORKFLOW.md`.
Reason: owner's requirement for staged, parallel, consistent, token-efficient work.

### D-007 — Database: MySQL 8
Accepted · 2026-10-30 · supersedes D-004
MySQL 8 (CHECK constraints from 8.0.16, mature JSON columns). Migrations may use MySQL 8 features.
Reason: owner's choice in the 2026-10-30 interview; matches the EDUNEXA developer notes.

### D-008 — Frontend: Inertia.js + plain Svelte
Accepted · 2026-10-30 · supersedes D-005
`@inertiajs/svelte` with Laravel routing. No SvelteKit.
Reason: owner confirmed the recommendation in D-005.

### D-009 — Product scope: content creation + administration from v1
Accepted · 2026-10-30
SIGRAK (working name) is an AI-assisted platform for making teaching content (Guided Generator, Template Engine, AI Provider Engine) and keeps EDUNEXA's school administration features. Both ship in v1. See `docs/drafts/product-concept.md`.
Reason: owner's concept message and interview.

### D-010 — Users and access
Accepted · 2026-10-30
Roles: teacher (class/subject), principal, school operator, supervisor (read-only over supervised schools), internal staff (separate Filament console and guard). Students are records, never users. Every page needs login; only login, invitation acceptance, and password reset are reachable without a session. No public pages or share links.
Reason: owner's concept message and interview.

### D-011 — How games reach students
Accepted · 2026-10-30
Present mode inside the teacher's session, plus a standalone offline HTML download (no login, no network calls, teaching content only).
Reason: students are not users and there are no public pages (D-010), but students still need to play.

### D-012 — AI keys are teacher-owned, several per teacher
Accepted · 2026-10-30
Each teacher adds one or more Gemini keys from Google projects they own. Round robin + failover run only within that teacher's keys. No school or platform key. Principals need no key. AI cost is never a SIGRAK cost. Supersedes the optional school fallback key in the EDUNEXA notes.
Reason: owner's concept message and interview. Gemini quota is per project.

### D-013 — Template storage: code + DB registry
Accepted · 2026-10-30
Template definitions, schemas, renderers, and game runtimes live in the repo. The DB holds the registry (active flag, versions) and optional prompt-instruction overrides edited in Filament, which create a new immutable version.
Reason: testable, versioned code with prompt tuning that needs no deploy.

### D-014 — v1 templates
Accepted · 2026-10-30
RPM (incl. kokurikuler variant), Bank Soal, LKPD, Quiz Battle, and Buat bebas (guided). Drag & Drop, Word Puzzle, Maze, and Flashcard come later.
Reason: owner's choice. Quiz Battle reuses Bank Soal, so the document-to-game path is proven early.

### D-015 — No AI-generated code in v1
Accepted · 2026-10-30
Games are built only from template runtimes. AI supplies content and allowed config values. Teachers' requests for missing features are logged for the team.
Reason: quality, consistency, and security.

### D-016 — Offline: Di Kelas only
Accepted · 2026-10-30
Attendance, journal, and student notes work offline and sync by `client_uuid`. Everything else needs a connection.
Reason: owner's choice. Keeps the PWA scope small.

### D-017 — Brand line
Accepted · 2026-10-30
Tagline "Kerja guru, lebih sigrak." Promise "Bahan ajar siap pakai dan administrasi beres, tanpa menulis prompt." The name SIGRAK stays a working name until checked in PDKI.
Reason: the old promise covered administration only.

### D-018 — Market: SD/MI, not limited to Kudus
Accepted · 2026-10-30
SD/MI grades 1–6 anywhere in Indonesia. Local content and the school calendar are configurable per region and per school. Kudus is the pilot and supplies the defaults. SMP/MTs come later, once curriculum data exists.
Reason: owner's choice. Supersedes "Kudus only" in the rebranding notes.

### D-019 — Validation flow
Accepted · 2026-10-30
Generated content is usable at once. Principal and supervisor can view and comment. Only school-changed or school-added curriculum goes through teacher → principal → supervisor.
Reason: owner kept the EDUNEXA rule.

### D-020 — Fresh build, curriculum-only import
Accepted · 2026-10-30
No Firebase migration of accounts, RPM, questions, or scores. Only curriculum JSON (KM-SD-2026-R2) is imported. Teachers are re-invited. Supersedes the staged migration plan in the EDUNEXA notes.
Reason: owner's choice. A new stack and a new data model.

### D-021 — Teacher navigation
Accepted · 2026-10-30
Hari Ini · Di Kelas · Buat · Asesmen · Kelas Saya. "Rencana" becomes "Buat" (template gallery, Buat bebas, Kurikulum, Kokurikuler). Dokumen Saya goes in the account menu.
Reason: owner's choice. Keeps a 5-item bottom nav.

### D-022 — AI engine technical choices
**Proposed** · 2026-10-30
(1) Own `AIProvider` interface with a Gemini adapter on Laravel's HTTP client, not the Laravel AI SDK (per-call key choice and raw error classification are core; the SDK can become the transport later).
(2) One generic `RunGeneration` job that runs the next step and re-dispatches itself.
(3) Progress by polling (Inertia partial reload), websockets later only if needed.
(4) Education logic lives in `app/Content/`, provider logic in `app/AI/`.
Reason: see `docs/drafts/ai-provider-engine.md`. Awaiting owner review.
