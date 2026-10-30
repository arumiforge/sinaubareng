# Project Context

Compact reference for agents. Update when the stack or a convention changes; record the reason in `DECISIONS.md`.

## Product

Not defined yet — the owner will supply scope later. Do not invent features.

## Backend

- **Framework:** Laravel (latest stable LTS-compatible release at project start).
- **Database:** MySQL or MariaDB — open decision, see `DECISIONS.md`. Write portable SQL/migrations; avoid engine-specific features until decided.
- **Cache / session / queue:** Redis.
- **Queue workers:** multiple workers, supervised (Supervisor or systemd). Laravel Horizon is the preferred dashboard since queues run on Redis.
- **Debugging:** Laravel Telescope (local/staging only; never enabled publicly in production).
- **Admin / back-office:** Filament.
- **Python:** only for tasks Laravel/Node cannot do well (e.g. data processing). Keep it in its own folder with pinned dependencies.

## Frontend

- **Inertia.js + Svelte** (Vite build, Node.js for tooling).
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
