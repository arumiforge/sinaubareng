# Server Monitoring and Alerts

- **Status:** draft
- **Task:** P2-08
- **Last updated:** 2026-10-31
- **Tools and free-tier limits checked online on 2026-10-31** (sources in [Tools](#tools)).

## Purpose

This guide tells the owner, quickly and reliably, when the SIGRAK server or one of its services is down, full, or failing. It also gives the exact steps to set this up. It covers the single VPS described in [`server-setup.md`](server-setup.md): IDCloudHost Jakarta, 8 vCPU / 16 GB / 320 GB, no public IP, Cloudflare Tunnel, and staging as a second site (D-073, D-086…D-088, D-095, D-096). It is for whoever sets up the server and whoever answers the alerts.

Owner answers (2026-10-31):

- **Channels:** Telegram and email.
- **Recipient:** the owner only.
- **Night:** urgent alerts may be sent at night (22.00–06.00 WIB); minor ones wait.

| Placeholder | Meaning |
|---|---|
| `<OWNER_EMAIL>` | Owner's email address for alerts |
| `<HC_API_KEY>` | Healthchecks.io project API key (read-write). Used once from a laptop; never stored on the server |
| `<HC_PING_KEY>` | Healthchecks.io project ping key (stored on the server) |
| `<HC_CH_TELEGRAM>`, `<HC_CH_EMAIL>` | Healthchecks.io integration (channel) UUIDs |
| `<REDIS_PASSWORD>`, `<GOTENBERG_USER>`, `<GOTENBERG_PASSWORD>` | Same values as in `server-setup.md` |

## Content

### 1. Design in one picture

The server has no public IP, so nothing outside can reach it except through the tunnel. Monitoring therefore works in two directions:

```text
                     ┌────────────────────────── Healthchecks.io ─────────────┐
 VPS: sigrak-health  │ expects a ping per check every 1 / 15 / 1440 minutes  │──► Telegram + email (urgent)
 (cron, every min)───┼─► ping = OK · /fail = broken · silence = server gone   │──► email only (minor)
                     └────────────────────────────────────────────────────────┘
 UptimeRobot ──HTTPS every 5 min──► Cloudflare ──tunnel──► app./ujian. /up     ──► email
 Cloudflare Notifications: tunnel health                                       ──► email
 Laravel Horizon: long queue waits (app code, Phase 3)                         ──► email
```

- **Push (dead man's switch).** A small script on the server checks each component every minute and pings Healthchecks.io. When a check fails twice in a row, it sends `/fail`, and the alert goes out at once. If the whole VPS dies, the pings stop, and Healthchecks.io alerts after the grace period. This uses outbound HTTPS only, which IDCloudHost confirmed is open.
- **Pull (outside view).** UptimeRobot opens `app.` and `ujian.` through Cloudflare every 5 minutes. It catches problems the server cannot see itself: DNS, Cloudflare settings, an Access rule that blocks the app by mistake. It also alerts if Healthchecks.io is down.
- **Severity by channel.** Urgent checks alert through **Telegram and email**. Minor checks alert by **email only**, so they never wake anyone; they are read in the morning. This is how the "urgent at night, minor waits" rule is met without a quiet-hours feature.

Nothing heavy runs on the VPS: one Bash script, one cron file, and `curl`.

### 2. Tools

Checked 2026-10-31.

| Tool | What we use | Free tier (as checked) | Works without public IP | Source |
|---|---|---|---|---|
| **Healthchecks.io** (Hobbyist) | Main alerting: 13 checks, Telegram + email | 20 checks, 3 team members, 100 log entries per check, Telegram and email integrations, no card. Its FAQ allows a Hobbyist account for company infrastructure (one account, ≤ 20 checks) | Yes: the server pings out | healthchecks.io/pricing, /faq, /docs/api, /docs/http_api |
| **UptimeRobot** (Free) | 2 external HTTP keyword monitors | 50 monitors, 5-minute interval, commercial use allowed (terms updated 2026-05-26). Free plan alerts by email (Telegram is a paid feature) | Yes: it checks the public Cloudflare hostnames | uptimerobot.com/terms, help.uptimerobot.com |
| **Cloudflare Notifications** | "Tunnel Health Alert" | Included with all Zero Trust plans, delivered by email or webhook | Yes | developers.cloudflare.com (Tunnel notifications) |
| **Laravel Horizon** | Long-wait notifications per queue (app config) | Part of Horizon; `Horizon::routeMailNotificationsTo()` and the `waits` setting | Yes: sends mail outbound | laravel.com/docs/13.x/horizon |

Considered and not chosen for now:

- **Netdata agent.** It can send Telegram alerts on its own and has many built-in alarms. It is not needed yet: the script covers the few thresholds we care about, with nothing extra to keep updated. Revisit if we need graphs or history.
- **Better Stack free.** It has 10 monitors and 3-minute checks, but the free plan alerts through email and Slack only (no Telegram), and its pricing page labels it "Free for personal projects".
- **Prometheus/Grafana.** Too heavy for one VPS at this stage.

If the team later wants more than 20 checks or stricter terms, Healthchecks.io Business costs $20/month (100 checks).

### 3. What is checked

Every component in `server-setup.md` has a check. **U** = urgent (Telegram + email), **M** = minor (email only).

| Healthchecks slug | Every | Fails when | Sev. | Notes |
|---|---|---|---|---|
| `prod-app` | 1 min | `https://app.sigrak.com/up` does not return 200 | U | Goes through Cloudflare and the tunnel, so it tests the whole path |
| `prod-ujian` | 1 min | `https://ujian.sigrak.com/up` does not return 200 | U | Highest priority during TKA sessions |
| `tunnel` | 1 min | `cloudflared` service not active | U | Tells "tunnel down" apart from "app down" |
| `host` | 1 min | Disk `/` ≥ 90 %; RAM available < 1.5 GB; OOM killer ran in the last 2 minutes | U | Message names the reason |
| `mysql` | 1 min | `mysqladmin ping` fails | U | Uses the read-only `backup` user |
| `redis` | 1 min | `PING` fails; memory ≥ 90 % of `maxmemory`; AOF write error | U | With `noeviction`, a full Redis stops queues and sessions |
| `horizon` | 1 min | `php artisan horizon:status` not running (exit code ≠ 0) | U | Horizon ≥ 5.25 returns distinct exit codes for paused and inactive |
| `scheduler` | 1 min | Supervisor program `sigrak-scheduler` not `RUNNING` | U | Replaced by an app heartbeat in Phase 3 (section 7) |
| `gotenberg` | 1 min | `GET /health` with basic auth does not return 200 | U | PDF exports and exam cards depend on it |
| `backup` | daily | Backup script fails, or no success ping within 26 h | U | Needs three lines added to the backup script (section 6) |
| `host-warn` | 15 min | Disk ≥ 80 %; swap used ≥ 50 %; 15-min load ≥ 8; MySQL connections ≥ 160; Redis memory ≥ 70 %; ≥ 5 failed jobs in 15 min | M | One message lists every warning |
| `security-updates` | daily | Security updates pending for > 1 day, or a reboot is required | M | `unattended-upgrades` installs most of them by itself |
| `staging` | 15 min | Staging `/up` (local request), or staging Horizon not running | M | Staging never pages anyone |

**Two strikes before `/fail`.** One failed `curl` can be a blip. The script counts failures in `/run/sigrak-health/`, and only the second failure in a row sends `/fail`. A single bad minute sends nothing, and the grace period covers it. A real outage therefore alerts within about 2 minutes.

**External, from UptimeRobot (email):**

| Monitor | Type | Target | Keyword |
|---|---|---|---|
| SIGRAK app | Keyword | `https://app.sigrak.com/up` | `Application up` |
| SIGRAK ujian | Keyword | `https://ujian.sigrak.com/up` | `Application up` |

`admin.` sits behind Cloudflare Access, so outside checkers cannot reach it. It shares Nginx, PHP-FPM, and the tunnel with `app.`, so `prod-app` covers it.

`/up` is Laravel's built-in health route (enabled in `bootstrap/app.php` with `health: '/up'`). It shows only "Application up" and no data, so it is the one technical exception to "every page sits behind login".

### 4. Healthchecks.io setup (once, from a laptop)

1. Sign up at healthchecks.io with `<OWNER_EMAIL>` (Hobbyist, free). Rename the default project to `SIGRAK production`.
2. **Integrations → Email:** add `<OWNER_EMAIL>`, and tick both "up" and "down".
3. **Integrations → Telegram:** in Telegram, open a chat with `@HealthchecksBot` and send `/start`. Open the confirmation link the bot returns, pick the project, and click **Connect Telegram**.
4. **Settings → API keys:** create a read-write API key (`<HC_API_KEY>`) and note the project's **Ping key** (`<HC_PING_KEY>`).
5. Look up the two channel UUIDs:

   ```bash
   curl -fsS https://healthchecks.io/api/v3/channels/ -H "X-Api-Key: <HC_API_KEY>"
   # note the "id" of the email and the telegram entries → <HC_CH_EMAIL>, <HC_CH_TELEGRAM>
   ```

6. Create the 13 checks. Running this again updates them, because `unique: ["slug"]` makes each call an upsert:

   ```bash
   API='<HC_API_KEY>'
   URGENT='<HC_CH_TELEGRAM>,<HC_CH_EMAIL>'
   MINOR='<HC_CH_EMAIL>'
   mk() {  # slug timeout_s grace_s channels tags
     curl -fsS https://healthchecks.io/api/v3/checks/ -H "X-Api-Key: $API" \
       --data "{\"name\":\"$1\",\"slug\":\"$1\",\"timeout\":$2,\"grace\":$3,\"channels\":\"$4\",\"tags\":\"$5\",\"unique\":[\"slug\"]}" \
       -o /dev/null -w "$1 %{http_code}\n"
   }
   for s in prod-app prod-ujian tunnel host mysql redis horizon scheduler gotenberg; do
     mk "$s" 60 120 "$URGENT" "prod urgent"
   done
   mk backup           86400 7200  "$URGENT" "prod urgent"
   mk host-warn        900   900   "$MINOR"  "prod minor"
   mk security-updates 86400 43200 "$MINOR"  "prod minor"
   mk staging          900   1800  "$MINOR"  "staging"
   ```

   Every line must print `201` (created) or `200` (updated). Do not store `<HC_API_KEY>` on the server; the server only needs the ping key.

### 5. Server setup

Settings file (root only):

```bash
sudo install -d -m 700 /etc/sigrak
sudo tee /etc/sigrak/monitoring.env > /dev/null <<'EOF'
HC_PING_KEY=<HC_PING_KEY>
REDIS_PASSWORD=<REDIS_PASSWORD>
GOTENBERG_USER=<GOTENBERG_USER>
GOTENBERG_PASSWORD=<GOTENBERG_PASSWORD>
EOF
sudo chmod 600 /etc/sigrak/monitoring.env
```

The check script. It reuses `/etc/sigrak/backup.cnf` (the read-only MySQL `backup` user from server-setup section 12):

```bash
sudo apt -y install update-notifier-common   # provides /usr/lib/update-notifier/apt-check
sudo tee /usr/local/sbin/sigrak-health > /dev/null <<'EOF'
#!/usr/bin/env bash
# Usage: sigrak-health minute|quarter|daily
set -uo pipefail
. /etc/sigrak/monitoring.env
HC="https://hc-ping.com/${HC_PING_KEY}"
STATE=/run/sigrak-health
mkdir -p "$STATE"
APP=/var/www/sigrak/current
STG=/var/www/sigrak-staging/current

# report <slug> <problems> [strikes]: empty problems = OK; /fail after <strikes> failures in a row (default 2).
report() {
  local slug=$1 problems=$2 strikes=${3:-2} f="$STATE/$1.fails" n
  if [ -z "$problems" ]; then
    rm -f "$f"
    curl -fsS -m 10 --retry 2 -o /dev/null "$HC/$slug"
    return
  fi
  n=$(( $(cat "$f" 2>/dev/null || echo 0) + 1 )); echo "$n" > "$f"
  echo "$(date -Iseconds) $slug: $problems"
  [ "$n" -ge "$strikes" ] && curl -fsS -m 10 --retry 2 -o /dev/null --data-raw "$problems" "$HC/$slug/fail"
}

# check <slug> <message> <command...>: run the command, report OK or the message.
check() {
  local slug=$1 msg=$2; shift 2
  if "$@" > /dev/null 2>&1; then report "$slug" ""; else report "$slug" "$msg"; fi
}
http_ok() { [ "$(curl -s -o /dev/null -m 15 -w '%{http_code}' "$@")" = 200 ]; }
redis() { redis-cli -a "$REDIS_PASSWORD" --no-auth-warning "$@"; }
redis_pct() {  # used_memory as % of maxmemory
  local used max
  used=$(redis INFO memory | awk -F: '/^used_memory:/{print $2+0}')
  max=$(redis CONFIG GET maxmemory | tail -1)
  [ "${max:-0}" -gt 0 ] && echo $(( used * 100 / max )) || echo 0
}
disk_pct() { df --output=pcent / | tail -1 | tr -dc '0-9'; }

minute() {
  local p
  check prod-app   "app /up not 200"       http_ok https://app.sigrak.com/up
  check prod-ujian "ujian /up not 200"     http_ok https://ujian.sigrak.com/up
  check tunnel     "cloudflared not active" systemctl is-active --quiet cloudflared

  p=""
  [ "$(disk_pct)" -ge 90 ] && p+="disk $(disk_pct)%; "
  [ "$(awk '/MemAvailable/{print int($2/1024)}' /proc/meminfo)" -lt 1536 ] && p+="RAM available < 1.5 GB; "
  journalctl -k --since "2 minutes ago" --no-pager -q 2>/dev/null | grep -qi 'out of memory' && p+="OOM killer ran; "
  report host "$p"

  check mysql "mysqladmin ping failed" mysqladmin --defaults-extra-file=/etc/sigrak/backup.cnf ping

  p=""
  if [ "$(redis PING 2>/dev/null)" != PONG ]; then p="no PONG"; else
    [ "$(redis_pct)" -ge 90 ] && p+="memory $(redis_pct)% of maxmemory; "
    redis INFO persistence | grep -q 'aof_last_write_status:ok' || p+="AOF write error; "
  fi
  report redis "$p"

  check horizon   "horizon not running"          sudo -u sigrak php "$APP/artisan" horizon:status
  check scheduler "sigrak-scheduler not RUNNING" sh -c 'supervisorctl status sigrak-scheduler | grep -q RUNNING'
  check gotenberg "gotenberg /health not 200"    http_ok -u "$GOTENBERG_USER:$GOTENBERG_PASSWORD" http://127.0.0.1:3000/health
}

quarter() {
  local p="" swap_total swap_free load conns failed
  [ "$(disk_pct)" -ge 80 ] && p+="disk $(disk_pct)%; "
  swap_total=$(awk '/SwapTotal/{print $2}' /proc/meminfo); swap_free=$(awk '/SwapFree/{print $2}' /proc/meminfo)
  [ "$swap_total" -gt 0 ] && [ $(( (swap_total - swap_free) * 100 / swap_total )) -ge 50 ] && p+="swap >= 50%; "
  load=$(awk '{print int($3)}' /proc/loadavg); [ "$load" -ge 8 ] && p+="load15 $load; "
  conns=$(mysql --defaults-extra-file=/etc/sigrak/backup.cnf -N -e "SHOW GLOBAL STATUS LIKE 'Threads_connected'" 2>/dev/null | awk '{print $2}')
  [ "${conns:-0}" -ge 160 ] && p+="mysql connections $conns; "
  [ "$(redis_pct)" -ge 70 ] && p+="redis memory $(redis_pct)%; "
  failed=$(mysql --defaults-extra-file=/etc/sigrak/backup.cnf -N -e \
    "SELECT COUNT(*) FROM sigrak.failed_jobs WHERE failed_at > NOW() - INTERVAL 15 MINUTE" 2>/dev/null)
  [ "${failed:-0}" -ge 5 ] && p+="$failed failed jobs in 15 min; "
  report host-warn "$p"

  p=""
  http_ok -H 'Host: staging-app.sigrak.com' http://127.0.0.1:8080/up || p+="staging /up not 200; "
  sudo -u sigrak-staging php "$STG/artisan" horizon:status > /dev/null 2>&1 || p+="staging horizon not running; "
  report staging "$p"
}

daily() {
  local p="" sec
  sec=$(/usr/lib/update-notifier/apt-check 2>&1 | cut -d';' -f2)
  [ "${sec:-0}" -gt 0 ] && p+="$sec security updates pending; "
  [ -f /var/run/reboot-required ] && p+="reboot required; "
  report security-updates "$p" 1   # daily check: alert on the first failure
}

case "${1:-minute}" in
  minute) minute ;; quarter) quarter ;; daily) daily ;;
esac
EOF
sudo chmod 700 /usr/local/sbin/sigrak-health
```

Run each mode once by hand. A run with no output means every check passed. Then confirm in Healthchecks.io that the checks turned green:

```bash
sudo /usr/local/sbin/sigrak-health minute
sudo /usr/local/sbin/sigrak-health quarter
sudo /usr/local/sbin/sigrak-health daily
```

Schedule (server time is UTC; `daily` runs at 01:05 UTC = 08:05 WIB, so it arrives during the day):

```bash
sudo tee /etc/cron.d/sigrak-health > /dev/null <<'EOF'
* * * * *    root /usr/local/sbin/sigrak-health minute  >> /var/log/sigrak/health.log 2>&1
*/15 * * * * root /usr/local/sbin/sigrak-health quarter >> /var/log/sigrak/health.log 2>&1
5 1 * * *    root /usr/local/sbin/sigrak-health daily   >> /var/log/sigrak/health.log 2>&1
EOF
```

`/var/log/sigrak/health.log` lists only failures. It is already covered by the `/var/log/sigrak/*.log` logrotate rule (server-setup section 13).

**Test the alert path.** Stop Gotenberg for three minutes, then start it again. A Telegram message and an email should arrive within about 2 minutes, followed by an "up" message:

```bash
cd /opt/gotenberg && sudo docker compose stop && sleep 180 && sudo docker compose start
```

### 6. Backup heartbeat (change for `server-setup.md`)

This task does not edit `server-setup.md`. The coordinator adds the following to the backup script in server-setup section 12, at integration:

1. Add `. /etc/sigrak/monitoring.env` and `HC="https://hc-ping.com/${HC_PING_KEY}"` after `umask 077`.
2. Add a start ping and a failure trap right after that:

   ```bash
   curl -fsS -m 10 --retry 2 -o /dev/null "$HC/backup/start"
   trap 'curl -fsS -m 10 --retry 2 -o /dev/null --data-raw "backup failed at line $LINENO" "$HC/backup/fail"' ERR
   ```

3. Add a success ping just before `echo "backup $STAMP ok"`:

   ```bash
   curl -fsS -m 10 --retry 2 -o /dev/null "$HC/backup"
   ```

With `set -euo pipefail` already in the script, any failing step (dump, tar, integrity test, R2 upload) sends `/fail`. A backup that never runs triggers the alert through silence (timeout 24 h, grace 2 h).

### 7. Other sources (dashboard and app settings)

**UptimeRobot.** Sign up with `<OWNER_EMAIL>` on the free plan. Add the two keyword monitors from section 3, at a 5-minute interval, with the default email alert contact.

**Cloudflare.** Go to the dashboard → Notifications (Observability → Alerts) → Add → **Tunnel Health Alert**, deliver to `<OWNER_EMAIL>`. Also add **Tunnel Creation or Deletion Event**: an unexpected deletion is a security event.

**Laravel app (Phase 3, to put in the backlog):**

- `Horizon::routeMailNotificationsTo(config('sigrak.alert_email'))` in `HorizonServiceProvider`, with `waits` set in `config/horizon.php`:
  - `redis:ai`: 600 s. AI jobs can wait legitimately behind the per-teacher funnel and quota cooldowns.
  - `redis:exports`: 120 s.
  - `redis:default`: 60 s.

  This needs the app's outbound mail provider; Horizon has no Telegram route.
- A scheduler heartbeat: a schedule entry every 5 minutes with `->pingOnSuccess(...)` to a Healthchecks.io check. It then replaces the Supervisor-status `scheduler` check (same slug; change its timeout to 300 s).
- On staging, set `APP_ENV=staging` so that Horizon notifications are off there, or point them to the same email. Staging never uses Telegram.

### 8. Alert routing

| Severity | Channel | When | Response |
|---|---|---|---|
| Urgent | Telegram + email | Any time, including 22.00–06.00 WIB | Look within 15 minutes; during a TKA session, at once |
| Minor | Email only | Any time (silent) | Next working morning |
| Outside view (UptimeRobot, Cloudflare) | Email | Any time | Same morning, or at once if the app is also red in Healthchecks.io |

- **TKA exam sessions.** `prod-ujian` is checked every minute and alerts within about 2 minutes. Before a scheduled session, open the Healthchecks.io dashboard and check that all urgent checks are green.
- **Mute during planned work.** Pause the affected checks in Healthchecks.io (dashboard → check → Pause) before a deploy or maintenance window, and resume them afterwards. A paused check resumes by itself on its next ping.
- **Only the owner** receives alerts, as decided. To add a second person later, add them as a Healthchecks.io team member (3 allowed on the free plan) and add a second Telegram or email integration.

### 9. Runbook

The first steps, in order, for each alert. SSH is `ssh ssh.sigrak.com`. If the tunnel is down, use the IDCloudHost web console instead.

| Alert | First checks | Usual fix |
|---|---|---|
| `prod-app` / `prod-ujian` down, `tunnel` green | 1. `curl -sI -H 'Host: app.sigrak.com' http://127.0.0.1:8080/up` 2. `sudo systemctl status nginx php8.5-fpm` 3. `tail -50 /var/www/sigrak/shared/storage/logs/laravel-$(date +%F).log` | Restart the failed service (`sudo systemctl restart php8.5-fpm` or `nginx`). After a bad deploy, point `current` back to the previous release (server-setup section 10) |
| `tunnel` down | 1. `sudo systemctl status cloudflared` 2. `sudo journalctl -u cloudflared -n 50` 3. `sudo cloudflared tunnel info sigrak-prod` | `sudo systemctl restart cloudflared`. If it cannot connect: test outbound with `curl -sI https://api.cloudflare.com`; if that fails too, it is an IDCloudHost network issue, so open a ticket |
| UptimeRobot down, Healthchecks.io green | 1. Open the site in a browser from a phone (mobile data) 2. Cloudflare dashboard: DNS records, Access applications, WAF events | Usually a Cloudflare setting (an Access rule covering `app.`, a WAF block, a deleted DNS record). Undo the recent change |
| `host`: disk ≥ 90 % | 1. `df -h /` 2. `sudo du -xh --max-depth=2 /var | sort -h | tail` 3. `ls -lh /var/backups/sigrak` | Remove old releases or local backups; check that logrotate runs; `sudo journalctl --vacuum-size=300M`. Plan a disk upgrade if usage grows steadily |
| `host`: RAM / OOM | 1. `free -h` 2. `sudo journalctl -k --since '10 minutes ago' | grep -i oom` 3. `ps aux --sort=-rss | head` | Usually Chromium in Gotenberg (capped at 2 GB) or too many PHP workers. Restart the culprit and lower `pm.max_children` or the Horizon `maxProcesses` |
| `mysql` down | 1. `sudo systemctl status mysql` 2. `sudo tail -50 /var/log/mysql/error.log` 3. `df -h /` | `sudo systemctl restart mysql`. A full disk is the most common cause |
| `redis` down or ≥ 90 % | 1. `redis-cli -a '<REDIS_PASSWORD>' --no-auth-warning INFO memory` 2. `... --bigkeys` 3. Horizon dashboard: pending jobs | When memory is full, writes fail (`noeviction`). Clear stale cache keys (`php artisan cache:clear` clears only the cache store when it has its own Redis database, as `architecture.md` proposes), find keys without a TTL, or raise `maxmemory` |
| `horizon` down | 1. `sudo supervisorctl status` 2. `tail -50 /var/log/sigrak/horizon.log` 3. Redis check above | `sudo supervisorctl restart sigrak-horizon`. If it was paused on purpose, run `php artisan horizon:continue` |
| `scheduler` down | 1. `sudo supervisorctl status sigrak-scheduler` 2. `tail -50 /var/log/sigrak/scheduler.log` | `sudo supervisorctl restart sigrak-scheduler` |
| `gotenberg` down | 1. `cd /opt/gotenberg && sudo docker compose ps` 2. `sudo docker compose logs --tail 50` 3. `free -h` | `sudo docker compose restart`. A memory-limit kill (exit 137) points to a very large export; find it in Horizon failed jobs |
| `backup` failed or missed | 1. `tail -50 /var/log/sigrak/backup.log` 2. `ls -lt /var/backups/sigrak | head` 3. `sudo rclone lsd offsite:<R2_BUCKET>` | Fix the failing step (MySQL credentials, disk, R2 token) and run `sudo /usr/local/sbin/sigrak-backup` by hand. Never leave a day without a good backup |
| `host-warn` | Read the email: it lists each warning | Plan the fix during working hours. A repeated "failed jobs" warning: open Horizon → Failed Jobs and look at the error class |
| `security-updates` | `sudo apt list --upgradable`; `cat /var/run/reboot-required.pkgs` | `sudo apt update && sudo apt -y upgrade`. Reboot outside school hours and never during a TKA session |
| `staging` | Same steps with the `sigrak-staging` names | Working hours only |
| Cloudflare tunnel deleted (unexpected) | Cloudflare audit log | Treat it as a security incident: rotate the Cloudflare account password and API tokens, and recreate the tunnel |

### 10. Where the logs are

| What | Where | Kept |
|---|---|---|
| Health check failures | `/var/log/sigrak/health.log` | 8 weeks (logrotate) |
| Laravel app | `/var/www/sigrak/shared/storage/logs/laravel-YYYY-MM-DD.log` | 14 days |
| Horizon, scheduler, backup | `/var/log/sigrak/{horizon,scheduler,backup}.log` | 8 weeks |
| Failed jobs | Horizon dashboard on `admin.sigrak.com`, or table `failed_jobs` | Until pruned by the app |
| Nginx | `/var/log/nginx/sigrak-{app,ujian,admin}.{access,error}.log` | Ubuntu default (14 days) |
| MySQL | `/var/log/mysql/error.log`, `/var/log/mysql/slow.log` | Ubuntu default |
| Tunnel | `sudo journalctl -u cloudflared` | Journal, capped at 500 MB |
| Gotenberg | `cd /opt/gotenberg && sudo docker compose logs` | 3 × 10 MB |
| Kernel / OOM | `sudo journalctl -k` | Journal |
| Alert history | Healthchecks.io (100 pings per check), UptimeRobot, Cloudflare alert history | Per service |

## Decisions to record

To be numbered during integration.

1. **Alert channels: Telegram and email, to the owner only; urgent alerts may be sent at night** (owner, 2026-10-31).
2. **Healthchecks.io (free Hobbyist) as the main alerting service.** A push-based dead man's switch fits a server without a public IP. There are 13 checks: urgent ones go to Telegram + email, minor ones to email only. Severity by channel replaces a quiet-hours feature.
3. **One Bash script (`/usr/local/sbin/sigrak-health`) run by cron** every minute, every 15 minutes, and daily, with two consecutive failures before `/fail`. No monitoring agent (Netdata, Prometheus) on the VPS for now.
4. **UptimeRobot free** for two external keyword checks of `app.` and `ujian.` `/up`, by email.
5. **Cloudflare Tunnel Health and Tunnel Creation/Deletion notifications** by email.
6. **Laravel's `/up` health route is enabled.** It is the only page reachable without login, and it shows no data.
7. **Thresholds:**
   - Urgent: disk ≥ 90 %; RAM available < 1.5 GB; OOM; Redis ≥ 90 % of `maxmemory`.
   - Minor: disk ≥ 80 %; swap ≥ 50 %; load15 ≥ 8; MySQL connections ≥ 160; Redis ≥ 70 %; ≥ 5 failed jobs in 15 min; pending security updates or a required reboot.
8. **Backup heartbeat:** the backup script pings `backup/start`, `backup`, and `backup/fail` (change to server-setup section 12, at integration).
9. **App work for Phase 3:** Horizon long-wait mail notifications (`waits`: `ai` 600 s, `exports` 120 s, `default` 60 s) and a scheduler heartbeat with `pingOnSuccess`.
10. **Staging alerts by email only**, from one combined `staging` check.

## Open questions

- [ ] Healthchecks.io's About page says the service is "free for hobby use", while its FAQ explicitly allows a Hobbyist account "for monitoring your company infrastructure" (≤ 20 checks). This guide follows the FAQ. If the owner prefers no doubt at all, Business costs $20/month.
- [ ] The app's outbound mail provider (needed for Horizon long-wait notifications and app mail) is not chosen yet.
- [ ] Should a second person be added as a backup recipient later (for example during school holidays, when the owner is away)?
