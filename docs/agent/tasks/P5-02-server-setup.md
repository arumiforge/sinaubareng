# P5-02 — Server, staging, and monitoring setup

- **Phase:** P5
- **Status:** todo
- **Owner:** —
- **Depends on:** —
- **Can run in parallel with:** P5-01

## Goal

Prepare the production VPS, the staging site on it, backups, and monitoring, by following the final guides step by step, so the first deploy has somewhere to go.

## Read first

- `docs/final/server-setup.md`
- `docs/final/server-monitoring.md`

## Output

- A running IDCloudHost VPS reached only through the Cloudflare Tunnel, with production and staging sites.
- No repository files, except corrections to the two guides if a step turns out wrong (then rebuild their PDFs from `docs/pdf/src/`, D-156).

## Done when

- [ ] Every check in `server-setup.md` section 14 passes for production, and the same checks pass for staging.
- [ ] One restore test from the local backup and one from the off-site R2 copy succeed.
- [ ] All 13 Healthchecks.io checks are green, and the Gotenberg stop test sends a Telegram and an email alert.
- [ ] Secrets are only in the team password manager, never in the repository.

## Result

Filled in by the agent or person on finish.
