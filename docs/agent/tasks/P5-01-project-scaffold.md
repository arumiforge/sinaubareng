# P5-01 — Project scaffold and CI

- **Phase:** P5
- **Status:** in-progress
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

- [ ] `composer install`, `npm ci`, `npm run build` work from a clean clone.
- [ ] `pint --test`, `phpstan`, `pest`, `npm run lint`, `npm run check` pass locally and in CI.
- [ ] `app.`, `ujian.`, and `admin.` hostnames each answer with their own placeholder page and guard.
- [ ] No Indonesian or English UI string literals outside `lang/id` (TR-1).
- [ ] Commits authored as the owner (D-148); the work is merged into `main` and the branch, if any, deleted.

## Result

Filled in by the agent on finish.
