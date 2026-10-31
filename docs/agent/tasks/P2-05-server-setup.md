# P2-05 — Server setup guide

- **Phase:** P2
- **Status:** review
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

- [x] Every component in CONTEXT.md covered, with versions verified online and the date checked
- [x] Commands are copy-pasteable; secrets shown as placeholders only

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-05-server-setup`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

2026-10-31 — `docs/drafts/server-setup.md` written. Versions checked online on 2026-10-30: Laravel 13, PHP 8.5 (≥ 8.5.12, Oct 2026 CVEs), MySQL 8.4.13 LTS, Redis 8.2.x (pinned), Gotenberg `8.39.0-chromium`, Node 24.22.2 LTS (build only). Covers base hardening, UFW (80/443 from Cloudflare only), PHP-FPM, MySQL, Redis, Nginx with `app.`, `ujian.`, and `admin.` vhosts, Docker + Gotenberg on localhost with basic auth and no outbound network, Cloudflare Tunnel + Access (no public IP), release-folder deploys, Supervisor (Horizon + `schedule:work`), backups with restore test, log rotation. 12 items under "Decisions to record".

- **Owner decision needed:** Ubuntu 22.04 standard support ends mid-2027, right after the first TKA season. The guide recommends starting on 24.04 instead (commands work unchanged). CONTEXT.md still says 22.04.
- 2026-10-31 owner answers applied: domain `sigrak.com`, IDCloudHost Cloud VPS Jakarta, off-site backups on Cloudflare R2 (rclone crypt), staging server required (section 15, `staging-*.sigrak.com` behind Cloudflare Access), Gotenberg `.env` keys fixed to the `spatie/laravel-pdf` v2 names, monitoring to be a separate task (not yet in `PROGRESS.md`; for the integration pass to add).
- 2026-10-31 more owner answers: Ubuntu 24.04 LTS, production VPS 8 vCPU / 16 GB, and no public IP, so all traffic (web + SSH) goes through a Cloudflare Tunnel with Cloudflare Access on `ssh.` and `admin.`. Origin CA, Authenticated Origin Pulls, and the Cloudflare IP allow-list were dropped.
- **For integration:** `CONTEXT.md` and `architecture.md` still say Ubuntu 22.04 and describe UFW with Cloudflare ranges + origin certificate; update them to 24.04 + Tunnel (decisions 7 and 19 in the guide). Add the monitoring task to `PROGRESS.md`.
- Still open: production disk and staging package sizes; confirm IDCloudHost outbound NAT (incl. port 7844) for a VM without a public IP.
- Follow-ups: monitoring/alerting task (owner confirmed); hostnames, UTC time and Gotenberg egress now aligned with P2-04 `architecture.md`; scheduler runs as `schedule:work` under Supervisor (task spec) where architecture shows cron, same effect.
