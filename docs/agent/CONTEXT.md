# Project Context

Compact reference for agents. Update when the stack or a convention changes; record the reason in `DECISIONS.md`.

## Product

**SIGRAK** (working name): a web app for SD/MI teachers and school leaders. It does AI-assisted content creation (RPM, Bank Soal, LKPD, Quiz Battle, Paket Soal TKA) through one dedicated template per content type (no generic generator), plus school administration and a TKA practice exam (CBT). Teachers never write prompts. Students are records, never users. They only touch the separate exam site, with per-session exam cards. Login-only, no public pages. Each teacher brings their own Gemini **free-tier** keys (several, rotated, at their own risk). No student personal data in prompts.

- Product: `docs/drafts/product-concept.md`
- Content engine: `docs/drafts/content-engine.md` · AI layer: `docs/drafts/ai-provider-engine.md`
- Data model: `docs/drafts/data-model.md` · Design and UI copy: `docs/drafts/design-system.md`
- Architecture: `docs/drafts/architecture.md` · Server setup: `docs/drafts/server-setup.md` · Coding standards: `docs/drafts/coding-standards.md` · Export and localization: `docs/drafts/export-localization.md`
- Religious education (SD six religions, MI Kemenag subjects): `docs/drafts/religious-education.md` · prepared data: `docs/data/`
- Owner's original material: `docs/sources/` (input only; drafts win where they differ)

Do not invent features beyond these documents.

## Backend

- **Framework:** Laravel (latest stable LTS-compatible release at project start).
- **Database:** MySQL 8 (D-007).
- **Cache / session / queue:** Redis.
- **Queue workers:** multiple workers, supervised (Supervisor or systemd). Laravel Horizon is the preferred dashboard since queues run on Redis.
- **Debugging:** Laravel Telescope (local/staging only; never enabled publicly in production).
- **Admin / back-office:** Filament, for internal staff only (separate guard; D-010).
- **AI:** Gemini free tier only, through our own `app/AI` layer. Default model Gemini 3.8 Flash (D-117). Keys are teacher-owned and encrypted, and a key can be marked as backup (D-012, D-116, D-023, D-119). One generation job per content type (D-024).
- **Mail:** Gmail SMTP (App Password), transactional only (D-110).
- **Documents:** PDF via Gotenberg 8 (Chromium) + `spatie/laravel-pdf`, one HTML view per PDF. Excel via Laravel Excel (`maatwebsite/excel`). DOCX via PhpWord. Letterhead is an uploaded image (D-028, D-031).
- **Rendering libraries:** three.js (figures), KaTeX (math), ApexCharts (charts), Lucide icons, Plus Jakarta Sans + an Arabic font. Pinned and bundled, no CDN (D-033).
- **Exam site:** own Inertia + Svelte entry, guard, and hostname for TKA practice participants, plus a JSON autosave endpoint. No AI (D-027, D-094). One session = one grade 6 class of 10–25 (D-092).
- **Python:** only for tasks Laravel/Node cannot do well (e.g. data processing). Keep it in its own folder with pinned dependencies.

## Frontend

- **Inertia.js + plain Svelte 5** (runes; `@inertiajs/svelte`, no SvelteKit; D-008, D-076) with **TypeScript** (`lang="ts"`, D-075). Vite build, Node.js for tooling.
- **Game runtime:** vanilla JS + CSS bundles per game template, playable standalone and offline (D-011).
- **Styling:** Tailwind CSS. Use **TailAdmin** as the UI template/reference; port its components to Svelte.
- **Responsive:** every screen must work on desktop and mobile. The school app is an installable PWA (D-093).
- **UI text:** Indonesian, user-friendly, through `lang/id` translation files. Translation keys in English.

## Infrastructure

- VPS on **Ubuntu Server 24.04 LTS** (D-086), **IDCloudHost Cloud VPS, Jakarta**, 8 vCPU / 16 GB RAM / 320 GB (D-070, D-087, D-096). **Staging** is a second, isolated site on the same VPS, behind Cloudflare Access (D-095).
- Domain **`sigrak.com`** (D-069). Files on **local disk** (D-114). Off-site backups on **Cloudflare R2**, encrypted (D-071); R2 is otherwise only an optional store for small, rarely read files (free tier).
- **Nginx** + **PHP-FPM**.
- **Docker** only for the Gotenberg PDF service (internal network, pinned image; D-028).
- **Cloudflare** in front (DNS, proxy, TLS, WAF). The VPS has no public IP: all traffic arrives through a **Cloudflare Tunnel** (`cloudflared`), and SSH and `admin.` sit behind Cloudflare Access (D-088). Nginx listens on localhost and restores the real client IP from `CF-Connecting-IP`.
- Node.js on the server only if needed at runtime; prefer building assets in CI/locally.

## Conventions

- Naming in English: classes, methods, variables, tables, columns, routes, config keys.
- Laravel defaults: PSR-12, plural snake_case tables, singular StudlyCase models.
- Exports (Excel/CSV/Word/PDF) and generated PDFs: Indonesian labels. Map English field names to Indonesian through translation keys, e.g. `__('export.columns.created_at')` → "Tanggal Dibuat".
- Dates/numbers in exports and UI: Indonesian format (`d/m/Y`, `1.234.567,89`, `Rp`), timezone `Asia/Jakarta` unless decided otherwise.
- Tests: **Pest** (D-074), run by **GitHub Actions** on every PR to `main` (D-084). Code rules: `docs/drafts/coding-standards.md`.
- Migrations are forward-only; `down()` is optional (D-085).
- No AI attribution anywhere (see `CLAUDE.md`).
