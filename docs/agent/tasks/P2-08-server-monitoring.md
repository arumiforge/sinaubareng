# P2-08 — Server monitoring and alerts

- **Phase:** P2
- **Status:** todo
- **Owner:** —
- **Depends on:** — (P2-05 server setup is merged)
- **Can run in parallel with:** P2-07

## Goal

Write one guide that tells the team, quickly and reliably, when SIGRAK's production server or one of its services is down, full, or failing, and that sets up those checks with exact commands. The guide covers the single VPS described in `server-setup.md`: IDCloudHost Jakarta, no public IP, Cloudflare Tunnel, and staging as a second site (D-073, D-088, D-095).

## Read first

- `docs/agent/CONTEXT.md`
- `docs/drafts/server-setup.md` (all sections; especially 9 Tunnel, 11 Supervisor/Horizon, 12 backups, 15 staging)
- `docs/drafts/architecture.md` (deployment topology, scheduled jobs, security boundaries)
- `docs/drafts/ai-provider-engine.md` (queues, failure classes, what Tim SIGRAK sees)
- `docs/agent/DECISIONS.md` only for D-073, D-086…D-088, D-095, D-096 (read only)

## Output

- `docs/drafts/server-monitoring.md` (use `docs/templates/DOC.md`), covering:
  - **What to watch, with thresholds:**
    - Uptime of `app.`, `ujian.`, and `admin.sigrak.com` from outside. Use Laravel's `/up` health route or a dedicated one; `admin.` sits behind Cloudflare Access.
    - Tunnel health: `cloudflared` connections, and Cloudflare's tunnel health notifications.
    - Host: disk (320 GB), RAM/swap, CPU load, and the OOM killer.
    - MySQL: up, connections, slow queries, disk use.
    - Redis: up, memory against `maxmemory` with `noeviction`, and AOF.
    - Horizon: supervisors running, failed jobs, and long wait times per queue (`ai`, `exports`, `default`).
    - Scheduler heartbeat.
    - Gotenberg `/health`.
    - The daily backup and its R2 upload, as a heartbeat (a missed run must alert).
    - Security updates pending or needing a reboot.
    - Staging must never page anyone at night.
  - **Tools:** each must be free or very cheap, verified online with the date checked, and must work **without a public IP** (outbound pushes or external HTTP checks through Cloudflare only). Compare a few options (for example Cloudflare notifications, an external uptime checker, a cron-heartbeat service, Horizon's own notifications, and a lightweight agent such as Netdata or node_exporter) and recommend one small set. No new heavy stack on the VPS unless justified.
  - **Alert routing:** who gets what, through which channel (email, Telegram, WhatsApp, or other; ask the owner), quiet hours, and what counts as urgent. TKA exam sessions are the highest priority.
  - **Exact setup commands** for everything that runs on the server, written in the same style as `server-setup.md` (copy-pasteable, secrets as placeholders).
  - **Runbook:** for each alert, the first three things to check and the fix. Include "tunnel down", "disk almost full", "Horizon stopped", "backup missed", and "Gotenberg failing".
  - **Log access:** where to look (Laravel daily logs, Horizon, Nginx, `journalctl -u cloudflared`) and how long logs are kept (server-setup section 13).
  - A **"Decisions to record"** section.

## Done when

- [ ] Every component in server-setup.md has a check, a threshold, and an alert target
- [ ] Every tool works without a public IP; versions, prices, and free-tier limits verified online, with the date
- [ ] Commands are copy-pasteable; secrets shown as placeholders only
- [ ] Runbook covers each alert

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-08-server-monitoring`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. List decisions under "Decisions to record" in your output; they get numbered at integration.
- Do not edit `docs/drafts/server-setup.md`. If monitoring needs a change there (for example a heartbeat line in the backup script), describe it in your output and in Result.

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
