# Project Context

Compact reference for agents. Update when the stack or a convention changes; record the reason in `DECISIONS.md`.

## Product

**SIGRAK** (working name): a web app for SD/MI teachers and school leaders. It does AI-assisted content creation (RPM, Bank Soal, LKPD, Quiz Battle, Buat bebas) through templates and pedagogical wizards, plus school administration. Teachers never write prompts. Students are records, never users. Login-only, no public pages. Each teacher brings their own Gemini keys (several, rotated).

- Product: `docs/drafts/product-concept.md`
- Content engine: `docs/drafts/content-engine.md` · AI layer: `docs/drafts/ai-provider-engine.md`
- Data model: `docs/drafts/data-model.md` · Design and UI copy: `docs/drafts/design-system.md`
- Owner's original material: `docs/sources/` (input only; drafts win where they differ)

Do not invent features beyond these documents.

## Backend

- **Framework:** Laravel (latest stable LTS-compatible release at project start).
- **Database:** MySQL 8 (D-007).
- **Cache / session / queue:** Redis.
- **Queue workers:** multiple workers, supervised (Supervisor or systemd). Laravel Horizon is the preferred dashboard since queues run on Redis.
- **Debugging:** Laravel Telescope (local/staging only; never enabled publicly in production).
- **Admin / back-office:** Filament, for internal staff only (separate guard; D-010).
- **AI:** Gemini through our own `app/AI` layer; keys are teacher-owned and encrypted (D-012, D-022).
- **Python:** only for tasks Laravel/Node cannot do well (e.g. data processing). Keep it in its own folder with pinned dependencies.

## Frontend

- **Inertia.js + plain Svelte** (`@inertiajs/svelte`, no SvelteKit; D-008). Vite build, Node.js for tooling.
- **Game runtime:** vanilla JS + CSS bundles per game template, playable standalone and offline (D-011).
- **Styling:** Tailwind CSS. Use **TailAdmin** as the UI template/reference; port its components to Svelte.
- **Responsive:** every screen must work on desktop and mobile.
- **UI text:** Indonesian, user-friendly, through `lang/id` translation files. Translation keys in English.

## Infrastructure

- VPS on **Ubuntu Server 22.04 LTS**.
- **Nginx** + **PHP-FPM**.
- **Cloudflare** in front (DNS, proxy, TLS, WAF). Nginx must restore the real client IP from Cloudflare headers.
- Node.js on the server only if needed at runtime; prefer building assets in CI/locally.

## Conventions

- Naming in English: classes, methods, variables, tables, columns, routes, config keys.
- Laravel defaults: PSR-12, plural snake_case tables, singular StudlyCase models.
- Exports (Excel/CSV/Word/PDF) and generated PDFs: Indonesian labels. Map English field names to Indonesian through translation keys, e.g. `__('export.columns.created_at')` → "Tanggal Dibuat".
- Dates/numbers in exports and UI: Indonesian format (`d/m/Y`, `1.234.567,89`, `Rp`), timezone `Asia/Jakarta` unless decided otherwise.
- No AI attribution anywhere (see `CLAUDE.md`).
