# P5-01 — Project scaffold and CI

- **Phase:** P5
- **Status:** done
- **Owner:** pw/jolly-fermi-c64iie
- **Depends on:** —
- **Can run in parallel with:** P5-02

## Goal

Create the empty Laravel application in this repository with the agreed stack, folder layout, and checks, so slice 1 can start on a green CI.

## Read first

- `docs/agent/CONTEXT.md`
- `docs/final/coding-standards.md` (sections 1–3, 6, 9, 12–13)
- `docs/final/architecture.md` (Components, Hostnames, Module boundaries)
- `docs/final/export-localization.md` (section 1, LOC-9…LOC-13)
- `docs/final/design-system.md` (Stack mapping, Colour tokens)

## Output

- Laravel 13 app at the repository root (PHP 8.5, MySQL 8.4).
- Inertia + Svelte 5 + TypeScript, Tailwind with `resources/css/tokens.css`, the TailAdmin layout ported to Svelte (shell only), Plus Jakarta Sans and Lucide bundled by Vite.
- Filament 5 panel on the `staff` guard; three route files (`routes/app.php`, `routes/exam.php`, `routes/admin.php`) registered with `Route::domain(...)`.
- `lang/id` with the files from export-localization §1 (empty groups), app and fallback locale `id`; `Route::resourceVerbs` set to `buat`/`ubah` (D-149).
- Pest (with one architecture test per row of the module table), Larastan level 6, Pint, ESLint, `svelte-check`.
- `.github/workflows/ci.yml` running the GIT-4 checks on every push and PR to `main`, with a MySQL 8 service.
- `.env.example` with every key from `server-setup.md` section 10, values empty.

## Done when

- [x] `composer install`, `npm ci`, `npm run build` work from a clean clone.
- [x] `pint --test`, `phpstan`, `pest`, `npm run lint`, `npm run check` pass locally and in CI.
- [x] `app.`, `ujian.`, and `admin.` hostnames each answer with their own placeholder page and guard.
- [x] No Indonesian or English UI string literals outside `lang/id` (TR-1).
- [x] Commits authored as the owner (D-148); the work is merged into `main` and the branch, if any, deleted.

## Result

- Laravel 13.39 on PHP 8.5, Filament 5.11 (`staff` guard, admin host root, sign-in at `/masuk`), Inertia 3 + Svelte 5 + TypeScript, Tailwind 4 with `resources/css/tokens.css` mapped in `theme.css`, Horizon, Telescope (local and staging only, LOG-4 fields hidden), Ziggy (D-163).
- Hostnames: `routes/app.php`, `routes/exam.php`, `routes/admin.php` under `Route::domain(config('app.hosts.*'))` (D-164). `UseGuard` makes `web`, `exam`, or `staff` the default per host. The exam site has its own root view (`exam.blade.php`), Vite entry (`resources/exam-client/`), and translation share (`common`, `exam`); ESLint blocks imports from `resources/js` there.
- `StaffUser` and `ExamParticipant` models back the guards. Their tables come with slice 1 (staff sign-in) and P5-06 (exam).
- `lang/id`: all 32 files from export-localization §1; only `common`, `nav`, `exam` have keys so far. `t()` in `resources/js/lib/i18n.ts`. Page-level `translations:` middleware (LOC-9) and `lang:audit` (D-100) are not built yet; `tests/Feature/TranslationKeysTest.php` already fails on a missing literal key.
- Checks: Pint (`declare_strict_types`), Larastan level 6, Pest on MySQL (arch test per module row + LAY-5, hostname tests), ESLint, `svelte-check` with strict route names. `.github/workflows/ci.yml` runs them plus `composer audit`, `npm audit --omit=dev`, and the build, with a MySQL 8.4 service.
- Local setup: `cp .env.example .env`, fill `APP_*`, hosts, and `DB_*`, then `php artisan key:generate && php artisan migrate`, `npm ci && npm run dev`. Map the three `*.sigrak.test` hosts to 127.0.0.1.
- App timezone is `Asia/Jakarta` (D-165, supersedes D-082). The MySQL session uses `+07:00`; the final docs and PDFs were updated on 2026-11-02.
