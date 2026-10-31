# Server Setup Guide

- **Status:** draft
- **Task:** P2-05
- **Last updated:** 2026-10-31 (owner answers: domain, VPS, R2, staging, Ubuntu 24.04, 8 vCPU / 16 GB, Cloudflare Tunnel, 320 GB disk, staging on the same VPS)
- **Versions checked online on 2026-10-30** (sources in [Versions](#versions)).

## Purpose

A step-by-step guide to prepare the SIGRAK VPS (IDCloudHost Cloud VPS, Jakarta), which runs production and, as a second site, staging: the main app, the Filament back office, the TKA exam site, queue workers, and the Gotenberg PDF service. The VPS has **no public IP**: all web and SSH traffic arrives through a Cloudflare Tunnel (`cloudflared`), which only makes outbound connections. It is for whoever sets up or rebuilds the server. Run the sections in order. Every command is meant to be copied as-is. Replace only the placeholders below; never commit real values.

| Placeholder | Meaning |
|---|---|
| `app.sigrak.com` | School app |
| `ujian.sigrak.com` | TKA exam site (D-027) |
| `admin.sigrak.com` | Staff console (Filament) + Horizon |
| `ssh.sigrak.com` | SSH through the tunnel, behind Cloudflare Access |
| `<TUNNEL_ID>` | UUID printed by `cloudflared tunnel create` |
| `<TEAM_EMAILS>` | Emails of the team members allowed by Cloudflare Access |
| `<STAGING_DB_PASSWORD>` | MySQL password of the staging database user |
| `<DB_PASSWORD>`, `<DB_BACKUP_PASSWORD>`, `<MYSQL_ROOT_PASSWORD>` | MySQL passwords |
| `<REDIS_PASSWORD>` | Redis password |
| `<GOTENBERG_USER>`, `<GOTENBERG_PASSWORD>` | Gotenberg basic auth |
| `<R2_ACCOUNT_ID>`, `<R2_ACCESS_KEY_ID>`, `<R2_SECRET_ACCESS_KEY>` | Cloudflare R2 account ID and bucket-scoped API token |
| `<R2_BUCKET>` | R2 bucket for backups (suggested name: `sigrak-backups`) |
| `<BACKUP_CRYPT_PASSWORD>`, `<BACKUP_CRYPT_SALT>` | rclone crypt passwords (encrypt backups before upload) |

Generate each secret with `openssl rand -base64 32 | tr -d '/+=' | cut -c1-32` and keep it in the team password manager.

The hostnames, guards, and time handling follow [`architecture.md`](architecture.md) (P2-04): one Laravel codebase, three domain-routed groups, host-only session cookies, server and database in UTC with the app showing `Asia/Jakarta`.

## Versions

Checked on 2026-10-30. "Pin" means the version is fixed in config and only changed on purpose.

| Component | Choice | Why | Support (as checked) | Source |
|---|---|---|---|---|
| OS | **Ubuntu Server 24.04 LTS** (owner, 2026-10-31) | 22.04 loses standard support in mid-2027, right after the first TKA season | Standard support to mid-2029 | ubuntu.com list of releases |
| Laravel | 13.x | Current major (13.0 on 2026-03-17) | Bug fixes to Q3 2027, security to 2028-03-17 | laravel.com/docs/13.x/releases |
| PHP | **8.5** (≥ 8.5.12) from `ppa:ondrej/php` | Laravel 13 supports 8.3–8.5; 8.5 has active support to 2027-12-31, security to 2029-12-31. 8.5.12 fixes the Oct 2026 CVEs | php.net/supported-versions | php.net, launchpad.net/~ondrej |
| Nginx | Ubuntu 24.04 package (1.24 + Ubuntu security patches), listening on localhost only | Fewer moving parts; patched by Canonical | Follows OS | Ubuntu archive |
| MySQL | **8.4 LTS** (8.4.13 on 2026-10-20) from repo.mysql.com, component `mysql-8.4-lts` | MySQL 8 per D-007; 8.4 is the 8.x LTS line | Premier support to Apr 2029 | dev.mysql.com release notes, endoflife.date/mysql |
| Redis | **8.2.x** from packages.redis.io, pinned `6:8.2.*` (8.2.11 for noble) | 8.2 is an extended release (5-year fixes); 8.10 is newest but short-lived | To 2030-09-01 | redis.io, packages.redis.io |
| Horizon | latest major via Composer | Queue dashboard + supervisors on Redis | — | laravel.com/docs/13.x/horizon |
| Filament | 5.x | PHP 8.2+, Laravel 11.28+ | — | filamentphp.com/docs/5.x |
| Laravel Excel | `maatwebsite/excel` 4.x | 4.x supports Laravel 12/13 and PHP 8.5 (3.1 blocks PHP 8.5 via PhpSpreadsheet 1.x) | — | packagist.org/packages/maatwebsite/excel |
| Docker Engine | docker-ce from download.docker.com | Only for Gotenberg (D-028) | — | docs.docker.com |
| Gotenberg | **`gotenberg/gotenberg:8.39.0-chromium`** (released 2026-10-27) | Chromium-only variant: no LibreOffice needed (no Word-to-PDF, D-031), ~30% smaller | — | github.com/gotenberg/gotenberg/releases, Docker Hub |
| Node.js | **24 LTS** (24.22.2), build only | Maintenance LTS to 2028-04-30, mature with Vite/Tailwind. Node 26 became LTS on 2026-10-28; revisit after it settles | — | nodejs.org releases, nodejs/Release schedule |
| Cloudflare Tunnel | `cloudflared` **2026.10.7** from pkg.cloudflare.com (locally-managed tunnel) | VPS has no public IP; outbound-only connections on port 7844 | — | developers.cloudflare.com/tunnel, pkg.cloudflare.com |
| Cloudflare | Free plan: DNS, proxy, WAF, Zero Trust Access (free up to 50 users) | CONTEXT.md | — | developers.cloudflare.com |

## Content

### 1. Base system

Create the VM in the IDCloudHost console: **Cloud VPS, location Jakarta**, image **Ubuntu Server 24.04 LTS**, custom package **8 vCPU / 16 GB RAM / 320 GB NVMe disk**. Do **not** attach a public (floating) IP. The VM still needs outbound internet (NAT) for apt, GitHub, Gemini, R2, and the tunnel itself (TCP and UDP port 7844 to Cloudflare); check this with the provider before ordering.

Without a public IP, the first steps run in the **IDCloudHost web console** (the VM's screen in the browser). Log in with the user and password set when creating the VM, then switch to root (`sudo -i`):

```bash
apt update && apt -y full-upgrade
apt -y install curl gnupg ca-certificates lsb-release software-properties-common \
  unzip git acl ufw unattended-upgrades supervisor logrotate
timedatectl set-timezone Etc/UTC   # the app displays Asia/Jakarta; cron times below are UTC
hostnamectl set-hostname sigrak-prod
dpkg-reconfigure -plow unattended-upgrades   # answer "Yes"
curl -sS -m 10 -o /dev/null -w '%{http_code}\n' https://api.cloudflare.com/client/v4/   # any HTTP code = outbound works
```

Swap (4 GB), so a burst of PDF jobs does not trigger the OOM killer:

```bash
fallocate -l 4G /swapfile && chmod 600 /swapfile && mkswap /swapfile && swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab
echo 'vm.swappiness=10' > /etc/sysctl.d/99-swappiness.conf && sysctl --system
```

Users: an admin user with sudo for people, and a `sigrak` user that owns the code and runs PHP-FPM, Horizon, and the scheduler.

```bash
adduser --gecos "" admin && usermod -aG sudo admin
mkdir -p /home/admin/.ssh
echo 'ssh-ed25519 AAAA... you@laptop' >> /home/admin/.ssh/authorized_keys   # paste each admin's public key
chown -R admin:admin /home/admin/.ssh && chmod 700 /home/admin/.ssh && chmod 600 /home/admin/.ssh/authorized_keys

adduser --system --group --home /var/www/sigrak --shell /bin/bash sigrak
usermod -aG sigrak www-data      # Nginx reads public/ through the group
```

SSH hardening. SSH will only be reachable through the tunnel (section 9), and the IDCloudHost web console is not SSH, so these settings cannot lock you out.

```bash
cat > /etc/ssh/sshd_config.d/99-sigrak.conf <<'EOF'
PermitRootLogin no
PasswordAuthentication no
KbdInteractiveAuthentication no
EOF
sshd -t && systemctl reload ssh
```

fail2ban is not used: every SSH connection arrives from `cloudflared` on `127.0.0.1`, which fail2ban never bans, and Cloudflare Access already checks who may connect.

From here on, work as `admin` with `sudo` (in the console until section 9, then over SSH through the tunnel).

### 2. Firewall (UFW)

Nothing is accepted from outside: there is no public IP, and `cloudflared` reaches Nginx and SSH over `127.0.0.1`, which UFW always allows. UFW still guards the provider's private network.

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw enable
sudo ufw status verbose     # Status: active, no ALLOW rules
```

Docker writes its own iptables rules and bypasses UFW for published ports. That is why Gotenberg is published on `127.0.0.1` only (section 7); never publish it as `3000:3000`.

### 3. PHP 8.5 + PHP-FPM

```bash
sudo LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt -y install php8.5-fpm php8.5-cli php8.5-common php8.5-mysql php8.5-redis \
  php8.5-mbstring php8.5-xml php8.5-curl php8.5-zip php8.5-gd php8.5-intl php8.5-bcmath
php -v    # must show 8.5.12 or later
```

PHP 8.5 always includes OPcache; there is no separate `php8.5-opcache` package. The extensions cover Laravel, Laravel Excel (zip, xml, gd, iconv), PhpWord (zip, xml), and Horizon (redis/pcntl, pcntl is built into the CLI).

PHP settings for both FPM and CLI:

```bash
sudo tee /etc/php/8.5/fpm/conf.d/99-sigrak.ini > /dev/null <<'EOF'
expose_php = Off
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 60
date.timezone = UTC
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 20000
opcache.validate_timestamps = 0
EOF
sudo tee /etc/php/8.5/cli/conf.d/99-sigrak.ini > /dev/null <<'EOF'
date.timezone = UTC
EOF
```

`opcache.validate_timestamps = 0` means PHP-FPM must be reloaded on every deploy (section 11 does this).

FPM pool running as `sigrak`:

```bash
sudo tee /etc/php/8.5/fpm/pool.d/sigrak.conf > /dev/null <<'EOF'
[sigrak]
user = sigrak
group = sigrak
listen = /run/php/php8.5-fpm-sigrak.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 60
pm.start_servers = 8
pm.min_spare_servers = 4
pm.max_spare_servers = 12
pm.max_requests = 500
request_terminate_timeout = 65s
EOF
sudo mv /etc/php/8.5/fpm/pool.d/www.conf /etc/php/8.5/fpm/pool.d/www.conf.disabled
sudo systemctl restart php8.5-fpm && sudo systemctl enable php8.5-fpm
```

`pm.max_children` ≈ (RAM left for PHP) ÷ (average worker size, about 60–80 MB). 60 (about 4–5 GB) fits the 16 GB VPS next to MySQL, Redis, Horizon, and Gotenberg. Check with `ps -o rss= -C php-fpm8.5 | sort -n | tail -3`.

Composer (installer checked against the published signature):

```bash
cd /tmp
EXPECTED="$(curl -fsSL https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
if [ "$EXPECTED" = "$ACTUAL" ]; then
  sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
else
  echo "Composer installer checksum mismatch, not installing"
fi
rm -f composer-setup.php
composer --version
```

### 4. MySQL 8.4 LTS

Ubuntu 24.04 ships MySQL 8.0 (end of life since April 2026), so use Oracle's APT repository with the `mysql-8.4-lts` component, which never jumps to 9.x by itself.

```bash
curl -fsSL https://repo.mysql.com/RPM-GPG-KEY-mysql-2025 | sudo gpg --dearmor -o /usr/share/keyrings/mysql.gpg
echo "deb [signed-by=/usr/share/keyrings/mysql.gpg] http://repo.mysql.com/apt/ubuntu $(lsb_release -cs) mysql-8.4-lts" \
  | sudo tee /etc/apt/sources.list.d/mysql.list
sudo apt update
apt-cache policy mysql-server     # candidate must be 8.4.x from repo.mysql.com
sudo apt -y install mysql-server   # set <MYSQL_ROOT_PASSWORD> when asked; choose strong password encryption
```

Server settings:

```bash
sudo tee /etc/mysql/mysql.conf.d/zz-sigrak.cnf > /dev/null <<'EOF'
[mysqld]
bind-address = 127.0.0.1
mysqlx = OFF
character-set-server = utf8mb4
collation-server = utf8mb4_0900_ai_ci
default-time-zone = '+00:00'
innodb_buffer_pool_size = 5G
max_connections = 200
slow_query_log = ON
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 1
EOF
sudo systemctl restart mysql && sudo systemctl enable mysql
```

`innodb_buffer_pool_size`: about 30% of RAM on this shared box (5G of 16 GB).

Database, app user, and a read-only backup user:

```bash
sudo mysql -uroot -p <<'EOF'
CREATE DATABASE sigrak CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER 'sigrak'@'localhost' IDENTIFIED BY '<DB_PASSWORD>';
GRANT ALL PRIVILEGES ON sigrak.* TO 'sigrak'@'localhost';
CREATE USER 'backup'@'localhost' IDENTIFIED BY '<DB_BACKUP_PASSWORD>';
GRANT SELECT, SHOW VIEW, TRIGGER, LOCK TABLES, EVENT ON sigrak.* TO 'backup'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### 5. Redis 8.2

Ubuntu 24.04 ships Redis 7.0, so use the official repository and pin the 8.2 line:

```bash
curl -fsSL https://packages.redis.io/gpg | sudo gpg --dearmor -o /usr/share/keyrings/redis-archive-keyring.gpg
sudo chmod 644 /usr/share/keyrings/redis-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/redis-archive-keyring.gpg] https://packages.redis.io/deb $(lsb_release -cs) main" \
  | sudo tee /etc/apt/sources.list.d/redis.list
sudo tee /etc/apt/preferences.d/redis > /dev/null <<'EOF'
Package: redis redis-server redis-sentinel redis-tools
Pin: version 6:8.2.*
Pin-Priority: 1001
EOF
sudo apt update
sudo apt -y install redis redis-server redis-tools
redis-server --version    # must show 8.2.x
```

Settings. `noeviction` is required: queues and Horizon data must never be evicted. Cache entries therefore always need a TTL.

```bash
sudo tee -a /etc/redis/redis.conf > /dev/null <<'EOF'

# --- SIGRAK ---
bind 127.0.0.1 -::1
protected-mode yes
requirepass <REDIS_PASSWORD>
maxmemory 2gb
maxmemory-policy noeviction
appendonly yes
appendfsync everysec
EOF
sudo systemctl restart redis-server && sudo systemctl enable redis-server
redis-cli -a '<REDIS_PASSWORD>' --no-auth-warning ping    # PONG
```

Redis 8 is licensed AGPLv3/RSALv2/SSPLv1. We run it unmodified as an internal service, which none of these restrict.

### 6. Nginx

Nginx listens on `127.0.0.1:8080` only, in plain HTTP. TLS ends at Cloudflare's edge, and the hop from Cloudflare to the server runs inside the encrypted tunnel. Cloudflare's **Always Use HTTPS** setting (section 9) does the HTTP → HTTPS redirect.

```bash
sudo apt -y install nginx
sudo rm -f /etc/nginx/sites-enabled/default
sudo sed -i 's/# server_tokens off;/server_tokens off;/' /etc/nginx/nginx.conf
```

Real client IP: every request comes from `cloudflared` on localhost, carrying the visitor's address in `CF-Connecting-IP`. Nginx swaps it in, so Laravel logs, throttling, and exam audit trails see the real IP.

```bash
sudo tee /etc/nginx/conf.d/realip.conf > /dev/null <<'EOF'
set_real_ip_from 127.0.0.1;
set_real_ip_from ::1;
real_ip_header CF-Connecting-IP;
EOF
```

Shared snippet for the three vhosts. `fastcgi_param HTTPS on` tells Laravel the visitor used HTTPS, so URLs and secure cookies are right without trusting proxy headers.

```bash
sudo tee /etc/nginx/snippets/sigrak-common.conf > /dev/null <<'EOF'
root /var/www/sigrak/current/public;
index index.php;
charset utf-8;

add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location /build/ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    try_files $uri =404;
}
location = /favicon.ico { access_log off; log_not_found off; }
location = /robots.txt  { access_log off; log_not_found off; }
error_page 404 /index.php;

location ~ ^/index\.php(/|$) {
    fastcgi_pass unix:/run/php/php8.5-fpm-sigrak.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    fastcgi_param HTTPS on;
    fastcgi_hide_header X-Powered-By;
    fastcgi_read_timeout 60s;
}
location ~ /\.(?!well-known).* { deny all; }
location ~ \.php$ { return 404; }
EOF
```

School app vhost (`app.sigrak.com`):

```bash
sudo tee /etc/nginx/sites-available/sigrak-app.conf > /dev/null <<'EOF'
server {
    listen 127.0.0.1:8080;
    server_name app.sigrak.com;
    client_max_body_size 25m;
    access_log /var/log/nginx/sigrak-app.access.log;
    error_log  /var/log/nginx/sigrak-app.error.log;
    include snippets/sigrak-common.conf;
}
EOF
```

Exam site vhost (`ujian.sigrak.com`). Same codebase, its own logs and a small upload limit (the exam site takes answers, not files). Do not add per-IP rate limits here: a whole class often shares one school IP.

```bash
sudo tee /etc/nginx/sites-available/sigrak-ujian.conf > /dev/null <<'EOF'
server {
    listen 127.0.0.1:8080;
    server_name ujian.sigrak.com;
    client_max_body_size 1m;
    access_log /var/log/nginx/sigrak-ujian.access.log;
    error_log  /var/log/nginx/sigrak-ujian.error.log;
    include snippets/sigrak-common.conf;
}
EOF
```

Staff console vhost (`admin.sigrak.com`: Filament and Horizon behind the `staff` guard, and behind Cloudflare Access, section 9):

```bash
sudo tee /etc/nginx/sites-available/sigrak-admin.conf > /dev/null <<'EOF'
server {
    listen 127.0.0.1:8080;
    server_name admin.sigrak.com;
    client_max_body_size 25m;
    access_log /var/log/nginx/sigrak-admin.access.log;
    error_log  /var/log/nginx/sigrak-admin.error.log;
    include snippets/sigrak-common.conf;
}
EOF
```

Catch-all for any other host name:

```bash
sudo tee /etc/nginx/sites-available/00-default.conf > /dev/null <<'EOF'
server {
    listen 127.0.0.1:8080 default_server;
    server_name _;
    return 444;
}
EOF
sudo ln -s /etc/nginx/sites-available/00-default.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/sigrak-app.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/sigrak-ujian.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/sigrak-admin.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
curl -s -o /dev/null -w '%{http_code}\n' -H 'Host: app.sigrak.com' http://127.0.0.1:8080/   # 404 until the first deploy, then 200 or 302
```

### 7. Docker + Gotenberg

Docker Engine from Docker's repository:

```bash
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" \
  | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update
sudo apt -y install docker-ce docker-ce-cli containerd.io docker-compose-plugin
sudo tee /etc/docker/daemon.json > /dev/null <<'EOF'
{
  "log-driver": "json-file",
  "log-opts": { "max-size": "10m", "max-file": "3" }
}
EOF
sudo systemctl restart docker && sudo systemctl enable docker
```

Gotenberg with a pinned image, bound to localhost, with basic auth, on its own Docker network without outbound access (the app sends every asset in the request, see `architecture.md`). The fonts folder holds Plus Jakarta Sans and the Arabic font (D-033) so PDFs render the same as the app.

```bash
sudo mkdir -p /opt/gotenberg/fonts
sudo tee /opt/gotenberg/.env > /dev/null <<'EOF'
GOTENBERG_API_BASIC_AUTH_USERNAME=<GOTENBERG_USER>
GOTENBERG_API_BASIC_AUTH_PASSWORD=<GOTENBERG_PASSWORD>
EOF
sudo chmod 600 /opt/gotenberg/.env
sudo tee /opt/gotenberg/compose.yaml > /dev/null <<'EOF'
services:
  gotenberg:
    image: gotenberg/gotenberg:8.39.0-chromium
    restart: unless-stopped
    ports:
      - "127.0.0.1:3000:3000"
    env_file: .env
    command:
      - "gotenberg"
      - "--api-enable-basic-auth"
      - "--api-timeout=120s"
    volumes:
      - ./fonts:/usr/local/share/fonts/sigrak:ro
    mem_limit: 2g
    networks:
      - gotenberg

networks:
  gotenberg:
    driver: bridge
    ipam:
      config:
        - subnet: 172.31.250.0/24
EOF
# copy the font files (.ttf/.otf) into /opt/gotenberg/fonts first, then:
cd /opt/gotenberg && sudo docker compose up -d
curl -s -o /dev/null -w '%{http_code}\n' -u '<GOTENBERG_USER>:<GOTENBERG_PASSWORD>' http://127.0.0.1:3000/health   # 200
curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:3000/forms/chromium/convert/html                         # 401 (auth works)
```

Block new connections **from** the container, both to the internet (Docker's `DOCKER-USER` chain) and to the host itself (`INPUT`). Requests from Laravel to `127.0.0.1:3000` and their replies keep working.

```bash
sudo tee /usr/local/sbin/gotenberg-egress > /dev/null <<'EOF'
#!/usr/bin/env bash
set -euo pipefail
NET=172.31.250.0/24
for CHAIN in DOCKER-USER INPUT; do
  iptables -C "$CHAIN" -s "$NET" -m conntrack --ctstate NEW -j DROP 2>/dev/null \
    || iptables -I "$CHAIN" 1 -s "$NET" -m conntrack --ctstate NEW -j DROP
done
EOF
sudo chmod 755 /usr/local/sbin/gotenberg-egress
sudo tee /etc/systemd/system/gotenberg-egress.service > /dev/null <<'EOF'
[Unit]
Description=Block outbound traffic from the Gotenberg container
After=docker.service
Requires=docker.service

[Service]
Type=oneshot
ExecStart=/usr/local/sbin/gotenberg-egress
RemainAfterExit=yes

[Install]
WantedBy=multi-user.target
EOF
sudo systemctl daemon-reload && sudo systemctl enable --now gotenberg-egress
cd /opt/gotenberg && sudo docker compose exec gotenberg bash -c 'timeout 5 bash -c "</dev/tcp/1.1.1.1/443" && echo OPEN || echo BLOCKED'   # BLOCKED
```

Upgrading Gotenberg is a deliberate change: edit the tag, `sudo docker compose pull && sudo docker compose up -d`, then print one PDF of each template before closing the change.

### 8. Node.js (build only)

Preferred: build front-end assets (`npm ci && npm run build`) in CI or on a developer machine and ship `public/build/` with the release. Then the server needs no Node at all.

If the server must build, install the pinned Node 24 LTS binary, checked against the official checksums:

```bash
NODE_VERSION=v24.22.2
cd /tmp
curl -fsSLO "https://nodejs.org/dist/${NODE_VERSION}/node-${NODE_VERSION}-linux-x64.tar.xz"
curl -fsSLO "https://nodejs.org/dist/${NODE_VERSION}/SHASUMS256.txt"
grep " node-${NODE_VERSION}-linux-x64.tar.xz\$" SHASUMS256.txt | sha256sum -c -
sudo mkdir -p /opt/node
sudo tar -xJf "node-${NODE_VERSION}-linux-x64.tar.xz" -C /opt/node --strip-components=1
sudo ln -sf /opt/node/bin/node /opt/node/bin/npm /opt/node/bin/npx /usr/local/bin/
node -v
```

Python is not installed for the app. Ubuntu's own `python3` stays as it is; any Python tool gets its own folder and virtualenv when a task needs it (CONTEXT.md).

### 9. Cloudflare Tunnel and Access

The tunnel connects outbound from the VPS to Cloudflare and carries the three web hostnames plus SSH. No DNS record points at the server; each hostname is a proxied CNAME to `<TUNNEL_ID>.cfargotunnel.com`.

Install `cloudflared` from Cloudflare's repository (in the IDCloudHost console):

```bash
curl -fsSL https://pkg.cloudflare.com/cloudflare-main.gpg | sudo tee /usr/share/keyrings/cloudflare-main.gpg > /dev/null
echo 'deb [signed-by=/usr/share/keyrings/cloudflare-main.gpg] https://pkg.cloudflare.com/cloudflared any main' \
  | sudo tee /etc/apt/sources.list.d/cloudflared.list
sudo apt update && sudo apt -y install cloudflared
cloudflared --version     # 2026.10.7 or later
```

Create the tunnel. `tunnel login` prints a URL: open it on your laptop, sign in to Cloudflare, and pick `sigrak.com`.

```bash
sudo cloudflared tunnel login                  # writes /root/.cloudflared/cert.pem
sudo cloudflared tunnel create sigrak-prod     # prints <TUNNEL_ID>, writes /root/.cloudflared/<TUNNEL_ID>.json
sudo install -d -m 755 /etc/cloudflared
sudo install -m 600 /root/.cloudflared/<TUNNEL_ID>.json /etc/cloudflared/<TUNNEL_ID>.json
sudo tee /etc/cloudflared/config.yml > /dev/null <<'EOF'
tunnel: <TUNNEL_ID>
credentials-file: /etc/cloudflared/<TUNNEL_ID>.json
ingress:
  - hostname: app.sigrak.com
    service: http://127.0.0.1:8080
  - hostname: ujian.sigrak.com
    service: http://127.0.0.1:8080
  - hostname: admin.sigrak.com
    service: http://127.0.0.1:8080
  - hostname: ssh.sigrak.com
    service: ssh://127.0.0.1:22
  - service: http_status:404
EOF
sudo cloudflared tunnel ingress validate --config /etc/cloudflared/config.yml
for h in app ujian admin ssh; do sudo cloudflared tunnel route dns sigrak-prod "$h.sigrak.com"; done
sudo cloudflared service install               # systemd unit using /etc/cloudflared/config.yml
sudo systemctl enable --now cloudflared
sudo cloudflared tunnel info sigrak-prod       # shows active connections
```

`cert.pem` is only needed to create tunnels and DNS routes, not to run one. Keep a copy in the password manager and remove it from the server: `sudo shred -u /root/.cloudflared/cert.pem`. The tunnel credentials JSON stays on the server (mode 600); treat it as a secret.

If UDP 7844 is blocked, `cloudflared` falls back to HTTP/2 over TCP 7844 by itself.

**Cloudflare Access** (Zero Trust dashboard → Access → Applications → Add → Self-hosted):

1. `ssh.sigrak.com`: policy "Allow" for `<TEAM_EMAILS>`. **Required**, so only the team can even reach the SSH port.
2. `admin.sigrak.com`: the same policy. Recommended: the staff console then needs both an Access login and the `staff` guard login.
3. Never put `app.` or `ujian.` behind Access: teachers and students sign in with the app's own login.

**SSH from a laptop.** Install `cloudflared` on the laptop, then add to `~/.ssh/config`:

```text
Host ssh.sigrak.com
  User admin
  ProxyCommand cloudflared access ssh --hostname %h
```

`ssh ssh.sigrak.com` opens a browser for the Access login the first time, then connects with your SSH key. Once this works, do the rest of the setup over SSH. The IDCloudHost web console stays available as the emergency way in if the tunnel is down.

**Zone settings** (Cloudflare dashboard → `sigrak.com`):

- SSL/TLS → Edge Certificates: **Always Use HTTPS** on, **Minimum TLS Version** 1.2. Turn HSTS on only after a week without TLS problems. The encryption mode (Full/Strict) does not affect tunnel hostnames; leave it on **Full (strict)**.
- No Origin CA certificate, no Authenticated Origin Pulls, and no Cloudflare IP allow-list are needed: nothing can reach the server except through the tunnel.

**Things Cloudflare changes for the app:**

- Proxied requests time out after 100 seconds. AI generation and PDF export already run as queued jobs; no web request may wait for them.
- Free plan uploads are capped at 100 MB per request (ours are far below).
- Leave "I'm Under Attack" mode and aggressive bot rules **off** for `ujian.sigrak.com`: a class signs in at the same moment from one IP, which looks like an attack.
- Cloudflare does not cache HTML by default; keep it that way. Only `/build/*` is cached long-term.
- If `cloudflared` stops, the whole site is offline. systemd restarts it; update it monthly with `sudo apt update && sudo apt -y install --only-upgrade cloudflared && sudo systemctl restart cloudflared`.

### 10. App folders, environment, and first deploy

Layout: each release in its own folder, `current` points at the live one, and `shared` holds what survives deploys.

```text
/var/www/sigrak/
├── current -> releases/20261031-1000
├── releases/
└── shared/
    ├── .env
    └── storage/
```

```bash
sudo -u sigrak mkdir -p /var/www/sigrak/releases /var/www/sigrak/shared/storage/{app/public,framework/{cache,sessions,views},logs}
sudo chmod 750 /var/www/sigrak
sudo mkdir -p /var/log/sigrak && sudo chown sigrak:sigrak /var/log/sigrak
```

Production `.env` (`/var/www/sigrak/shared/.env`, owned by `sigrak`, mode 600). Only server-related keys are listed; the app adds its own.

```dotenv
APP_NAME=SIGRAK
APP_ENV=production
APP_DEBUG=false
APP_KEY=                       # generated once with: php artisan key:generate --show
APP_URL=https://app.sigrak.com
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_DAILY_DAYS=14
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sigrak
DB_USERNAME=sigrak
DB_PASSWORD=<DB_PASSWORD>

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=<REDIS_PASSWORD>

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
SESSION_SECURE_COOKIE=true
# SESSION_DOMAIN stays unset: app and exam site must not share cookies

TELESCOPE_ENABLED=false

# spatie/laravel-pdf v2 Gotenberg driver (config/laravel-pdf.php reads these keys)
LARAVEL_PDF_DRIVER=gotenberg
GOTENBERG_URL=http://127.0.0.1:3000
GOTENBERG_USERNAME=<GOTENBERG_USER>
GOTENBERG_PASSWORD=<GOTENBERG_PASSWORD>
```

**`APP_KEY` is critical.** Teachers' Gemini keys are encrypted with it (D-012). Losing it makes every stored AI key unreadable. Keep a copy in the password manager, outside the server and outside the backups.

Deploy script (run as `sigrak`). The app timezone `Asia/Jakarta` is set in `config/app.php`, not here.

```bash
sudo tee /usr/local/bin/sigrak-deploy > /dev/null <<'EOF'
#!/usr/bin/env bash
# Usage: sigrak-deploy <git-ref>   (run as the sigrak user)
set -euo pipefail
REF="${1:?git ref required}"
BASE=/var/www/sigrak
REL="$BASE/releases/$(date +%Y%m%d-%H%M%S)"

git clone --depth 1 --branch "$REF" git@github.com:arumiforge/sinaubareng.git "$REL"
cd "$REL"
rm -rf storage && ln -s "$BASE/shared/storage" storage
ln -s "$BASE/shared/.env" .env

composer install --no-dev --optimize-autoloader --no-interaction
# Assets: either public/build/ comes from CI, or build here when Node is installed:
[ -d public/build ] || { npm ci && npm run build && rm -rf node_modules; }

php artisan migrate --force
php artisan optimize
php artisan storage:link

ln -sfn "$REL" "$BASE/current"
sudo /usr/bin/systemctl reload php8.5-fpm
php artisan horizon:terminate
sudo /usr/bin/supervisorctl restart sigrak-scheduler

ls -1dt "$BASE"/releases/* | tail -n +6 | xargs -r rm -rf   # keep 5 releases
EOF
sudo chmod 755 /usr/local/bin/sigrak-deploy

sudo tee /etc/sudoers.d/sigrak-deploy > /dev/null <<'EOF'
sigrak ALL=(root) NOPASSWD: /usr/bin/systemctl reload php8.5-fpm, /usr/bin/supervisorctl restart sigrak-scheduler
EOF
sudo chmod 440 /etc/sudoers.d/sigrak-deploy && sudo visudo -c
```

The server pulls from GitHub with a read-only **deploy key**: `sudo -u sigrak ssh-keygen -t ed25519 -N "" -f /var/www/sigrak/.ssh/id_ed25519`, then add the public key in the repository settings under Deploy keys (read-only). Rollback: point `current` at the previous release folder and run the last three commands of the script.

### 11. Supervisor: Horizon and the scheduler

Horizon runs all queue workers. The queues and their worker counts live in the app's `config/horizon.php` (the `ai` queue gets its own supervisor, see `ai-provider-engine.md`). Supervisor only keeps the two processes alive.

```bash
sudo tee /etc/supervisor/conf.d/sigrak.conf > /dev/null <<'EOF'
[program:sigrak-horizon]
command=/usr/bin/php /var/www/sigrak/current/artisan horizon
user=sigrak
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/sigrak/horizon.log
stopwaitsecs=600

[program:sigrak-scheduler]
command=/usr/bin/php /var/www/sigrak/current/artisan schedule:work
user=sigrak
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/sigrak/scheduler.log
stopwaitsecs=60
EOF
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl status
```

`schedule:work` under Supervisor replaces the usual cron line (`* * * * * php artisan schedule:run`), which is the form `architecture.md` shows. Both run the same schedule; use only one.

`stopwaitsecs=600` is longer than the longest AI step (5 minutes per step, D-022 draft), so a deploy lets running jobs finish. Keep the queue's `retry_after` above the longest job timeout, as the AI engine doc requires.

Starting point for `config/horizon.php` on the 8 vCPU / 16 GB VPS (tune after real load):

| Supervisor | Queue | Processes | Timeout |
|---|---|---|---|
| `supervisor-ai` | `ai` | 4–12 (auto balance) | 330 s |
| `supervisor-exports` | `exports` | 4 | 180 s |
| `supervisor-default` | `default` | 3 | 60 s |

Exports call Gotenberg; four at a time fits the 2 GB container limit. AI workers mostly wait on Gemini, so their count is bounded by the teachers' free-tier quotas and the 2-per-teacher funnel, not by CPU.

### 12. Backups and restore test

What is backed up, daily at 01:30 WIB (18:30 UTC):

- MySQL: a consistent `mysqldump` of `sigrak`.
- Files: `shared/storage/app` (uploads such as letterheads, generated files).
- Not in the backup: `.env` and `APP_KEY` (kept in the password manager, see section 10).

Local copies are kept 14 days, off-site copies 30 days. The off-site target is **Cloudflare R2**, reached through `rclone` with a `crypt` remote, so files are encrypted before upload.

In the Cloudflare dashboard: R2 → Create bucket `<R2_BUCKET>` (location: automatic, private). Then R2 → Manage API tokens → Create token with **Object Read & Write**, scoped to that bucket only. Copy the Access Key ID and Secret Access Key; the Account ID is on the R2 overview page. Keep both crypt passwords in the password manager: without them the off-site copies cannot be read.

```bash
sudo -v ; curl -fsSL https://rclone.org/install.sh | sudo bash
rclone version | head -1    # must be 1.59 or newer for R2
sudo rclone config create offsite s3 provider=Cloudflare \
  access_key_id=<R2_ACCESS_KEY_ID> secret_access_key=<R2_SECRET_ACCESS_KEY> \
  endpoint=https://<R2_ACCOUNT_ID>.r2.cloudflarestorage.com region=auto acl=private no_check_bucket=true
sudo rclone config create offsite-crypt crypt remote=offsite:<R2_BUCKET>/production \
  password=<BACKUP_CRYPT_PASSWORD> password2=<BACKUP_CRYPT_SALT> --obscure
sudo rclone lsd offsite:<R2_BUCKET> && echo 'R2 reachable'

sudo install -d -m 700 /etc/sigrak /var/backups/sigrak
sudo tee /etc/sigrak/backup.cnf > /dev/null <<'EOF'
[client]
user=backup
password=<DB_BACKUP_PASSWORD>
EOF
sudo chmod 600 /etc/sigrak/backup.cnf

sudo tee /usr/local/sbin/sigrak-backup > /dev/null <<'EOF'
#!/usr/bin/env bash
set -euo pipefail
umask 077
STAMP="$(date +%Y%m%d-%H%M)"
DIR=/var/backups/sigrak

mysqldump --defaults-extra-file=/etc/sigrak/backup.cnf --single-transaction --quick \
  --no-tablespaces --triggers --events --hex-blob --set-gtid-purged=OFF sigrak \
  | gzip > "$DIR/db-$STAMP.sql.gz"
tar -C /var/www/sigrak/shared -czf "$DIR/storage-$STAMP.tar.gz" storage/app

gzip -t "$DIR/db-$STAMP.sql.gz"
tar -tzf "$DIR/storage-$STAMP.tar.gz" > /dev/null

rclone copy "$DIR" offsite-crypt:daily --include "*-$STAMP.*"
rclone delete offsite-crypt:daily --min-age 30d
find "$DIR" -type f -mtime +14 -delete
echo "backup $STAMP ok"
EOF
sudo chmod 700 /usr/local/sbin/sigrak-backup
echo '30 18 * * * root /usr/local/sbin/sigrak-backup >> /var/log/sigrak/backup.log 2>&1' | sudo tee /etc/cron.d/sigrak-backup
sudo /usr/local/sbin/sigrak-backup    # first run by hand
```

**Restore test — monthly, and after any change to this section.** A backup counts only after it has been restored once. On the server, into a throwaway database:

```bash
LATEST="$(ls -1t /var/backups/sigrak/db-*.sql.gz | head -1)"
sudo mysql -uroot -p -e "CREATE DATABASE sigrak_restore_test CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci"
gunzip -c "$LATEST" | sudo mysql -uroot -p sigrak_restore_test
sudo mysql -uroot -p -e "
  SELECT table_name, table_rows FROM information_schema.tables
  WHERE table_schema IN ('sigrak','sigrak_restore_test') ORDER BY table_name, table_schema;"
sudo mysql -uroot -p -e "DROP DATABASE sigrak_restore_test"

mkdir -p /tmp/restore-test && tar -xzf "$(ls -1t /var/backups/sigrak/storage-*.tar.gz | head -1)" -C /tmp/restore-test
du -sh /tmp/restore-test/storage/app /var/www/sigrak/shared/storage/app && rm -rf /tmp/restore-test
```

Row counts are estimates (`table_rows`), so compare them roughly; for key tables (`users`, `schools`, generated documents) run `SELECT COUNT(*)` on both. Twice a year, also restore from the **off-site** copy (`rclone copy offsite-crypt:daily/<file> /tmp/`) to prove the encryption password works. Write the date and result of each test in the ops log.

Full disaster restore: rebuild a VPS with sections 1–11, restore the dump into `sigrak`, extract `storage/app` into `shared/storage/`, put `.env` back with the **original** `APP_KEY`, deploy, then copy `/etc/cloudflared/` (config and credentials JSON) to the new VPS and start `cloudflared` there. No DNS change is needed, because the hostnames point at the tunnel, not at an IP.

### 13. Log rotation

| Log | Rotation |
|---|---|
| Laravel (`storage/logs/laravel-YYYY-MM-DD.log`) | By Laravel itself: `LOG_STACK=daily`, `LOG_DAILY_DAYS=14` |
| Nginx, MySQL, Redis, UFW | Ubuntu's packaged logrotate rules (keep them) |
| `/var/log/sigrak/*.log` (Horizon, scheduler, backup) | Rule below |
| Docker (Gotenberg) | `daemon.json`: 3 × 10 MB per container |
| systemd journal | Capped below |

```bash
sudo tee /etc/logrotate.d/sigrak > /dev/null <<'EOF'
/var/log/sigrak/*.log {
    weekly
    rotate 8
    missingok
    notifempty
    compress
    delaycompress
    copytruncate
    su root sigrak
}
EOF
sudo logrotate -d /etc/logrotate.d/sigrak    # dry run, check for errors

sudo mkdir -p /etc/systemd/journald.conf.d
printf '[Journal]\nSystemMaxUse=500M\n' | sudo tee /etc/systemd/journald.conf.d/size.conf
sudo systemctl restart systemd-journald
```

`copytruncate` is used because Supervisor keeps its log files open.

### 14. Final checks (production)

```bash
php -v | head -1                                   # PHP 8.5.12+
mysql --version                                    # 8.4.x
redis-server --version                             # 8.2.x
sudo docker ps --format '{{.Image}} {{.Ports}}'    # gotenberg 8.39.0-chromium on 127.0.0.1:3000
sudo ss -tlnp | grep -E ':(3306|6379|3000|33060)\b' # all on 127.0.0.1 / ::1 only; no 33060
sudo ss -tlnp | grep -E ':(80|443|8080)\b'          # only 127.0.0.1:8080 (nginx)
sudo ufw status verbose                            # active, deny incoming, no ALLOW rules
systemctl is-active cloudflared                    # active
sudo cloudflared tunnel info sigrak-prod           # connections listed
sudo supervisorctl status                          # horizon + scheduler RUNNING
curl -sI https://app.sigrak.com | head -1          # 200 or 302 to login
curl -sI https://admin.sigrak.com | head -1        # 302 to the Cloudflare Access login
```

In the app: log in, create one document, export it to PDF, and check in Horizon that the `ai` and `exports` queues ran the jobs. Open `ujian.sigrak.com` on a phone and check that it shows the exam sign-in and nothing from the main app. Check that Horizon opens only on `admin.sigrak.com` after staff login, and that Telescope is not reachable.

### 15. Staging (second site on the same VPS)

Staging runs **on the production VPS** as a second, fully separated site. It shares the services (MySQL, Redis, Gotenberg, Nginx, `cloudflared`) but has its own user, code, PHP-FPM pool, database, Redis keys, workers, and hostnames. Every app release goes to staging first.

**Trade-off.** Because the services are shared, upgrades of PHP, MySQL, Redis, Nginx, the Gotenberg image, or the OS cannot be tried on staging first. Staging tests app releases only. Rehearse big service upgrades on a temporary VM or on a VPS snapshot.

| Item | Production | Staging |
|---|---|---|
| System user | `sigrak` | `sigrak-staging` |
| App folder | `/var/www/sigrak` | `/var/www/sigrak-staging` (same `releases/`, `shared/`, `current` layout) |
| PHP-FPM pool | `sigrak`, 60 children | `sigrak-staging`, 10 children |
| MySQL | DB `sigrak`, user `sigrak` | DB `sigrak_staging`, user `sigrak_staging` (no rights on `sigrak`) |
| Redis | DB 0–3, default prefixes | DB 8–11, `sigrak_staging_` prefixes |
| Workers | `sigrak-horizon`, `sigrak-scheduler` | `sigrak-staging-horizon` (1 process per queue), `sigrak-staging-scheduler` |
| Hostnames | `app.`, `ujian.`, `admin.sigrak.com` | `staging-app.`, `staging-ujian.`, `staging-admin.sigrak.com` |
| Access | `ssh.` and `admin.` | All `staging-*` hostnames, team only |
| Logs | `/var/log/sigrak/` | `/var/log/sigrak-staging/` |
| Backups | Daily to R2 | None (seeded data only) |

User, folders, and PHP-FPM pool:

```bash
sudo adduser --system --group --home /var/www/sigrak-staging --shell /bin/bash sigrak-staging
sudo usermod -aG sigrak-staging www-data
sudo -u sigrak-staging mkdir -p /var/www/sigrak-staging/releases \
  /var/www/sigrak-staging/shared/storage/{app/public,framework/{cache,sessions,views},logs}
sudo chmod 750 /var/www/sigrak-staging
sudo mkdir -p /var/log/sigrak-staging && sudo chown sigrak-staging:sigrak-staging /var/log/sigrak-staging

sudo tee /etc/php/8.5/fpm/pool.d/sigrak-staging.conf > /dev/null <<'EOF'
[sigrak-staging]
user = sigrak-staging
group = sigrak-staging
listen = /run/php/php8.5-fpm-sigrak-staging.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 10
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
pm.max_requests = 500
request_terminate_timeout = 65s
EOF
sudo systemctl reload php8.5-fpm
```

Database and user. The staging user can only see `sigrak_staging`:

```bash
sudo mysql -uroot -p <<'EOF'
CREATE DATABASE sigrak_staging CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER 'sigrak_staging'@'localhost' IDENTIFIED BY '<STAGING_DB_PASSWORD>';
GRANT ALL PRIVILEGES ON sigrak_staging.* TO 'sigrak_staging'@'localhost';
FLUSH PRIVILEGES;
EOF
```

Nginx: one snippet and three vhosts, all on `127.0.0.1:8080` like production, with `noindex`:

```bash
sed -e 's#/var/www/sigrak/current/public#/var/www/sigrak-staging/current/public#' \
    -e 's#php8.5-fpm-sigrak.sock#php8.5-fpm-sigrak-staging.sock#' \
    /etc/nginx/snippets/sigrak-common.conf | sudo tee /etc/nginx/snippets/sigrak-staging-common.conf > /dev/null
echo 'add_header X-Robots-Tag "noindex, nofollow" always;' | sudo tee -a /etc/nginx/snippets/sigrak-staging-common.conf > /dev/null

for site in app ujian admin; do
  sudo tee "/etc/nginx/sites-available/sigrak-staging-$site.conf" > /dev/null <<EOF
server {
    listen 127.0.0.1:8080;
    server_name staging-$site.sigrak.com;
    client_max_body_size 25m;
    access_log /var/log/nginx/sigrak-staging-$site.access.log;
    error_log  /var/log/nginx/sigrak-staging-$site.error.log;
    include snippets/sigrak-staging-common.conf;
}
EOF
  sudo ln -s "/etc/nginx/sites-available/sigrak-staging-$site.conf" /etc/nginx/sites-enabled/
done
sudo nginx -t && sudo systemctl reload nginx
```

Tunnel: add the three hostnames to the **same** tunnel, above the final catch-all rule in `/etc/cloudflared/config.yml`. No separate SSH hostname is needed, because it is the same server.

```yaml
  - hostname: staging-app.sigrak.com
    service: http://127.0.0.1:8080
  - hostname: staging-ujian.sigrak.com
    service: http://127.0.0.1:8080
  - hostname: staging-admin.sigrak.com
    service: http://127.0.0.1:8080
```

```bash
sudo cloudflared tunnel ingress validate --config /etc/cloudflared/config.yml
for h in staging-app staging-ujian staging-admin; do sudo cloudflared tunnel route dns sigrak-prod "$h.sigrak.com"; done
sudo systemctl restart cloudflared
```

`tunnel route dns` needs `cert.pem`; restore it from the password manager for this step and shred it again afterwards. Then add **one** Cloudflare Access application covering the three `staging-*` hostnames, allowing only `<TEAM_EMAILS>`.

Staging `.env` (`/var/www/sigrak-staging/shared/.env`, owner `sigrak-staging`, mode 600). Start from the production list in section 10 and change these keys. The Redis prefixes keep staging Horizon from picking up production jobs:

```dotenv
APP_ENV=staging
APP_DEBUG=false
APP_KEY=                       # its own key; never reuse production's
APP_URL=https://staging-app.sigrak.com
LOG_LEVEL=debug

DB_DATABASE=sigrak_staging
DB_USERNAME=sigrak_staging
DB_PASSWORD=<STAGING_DB_PASSWORD>

REDIS_PREFIX=sigrak_staging_
REDIS_DB=8
REDIS_CACHE_DB=9
CACHE_PREFIX=sigrak_staging_
HORIZON_PREFIX=sigrak_staging_horizon:

MAIL_MAILER=log
TELESCOPE_ENABLED=true         # visible to staff only (architecture.md)
```

Redis `noeviction` is global, so staging cache keys need TTLs too. Gotenberg is shared (same container and credentials). It holds no data, but keep staging exports small.

Workers:

```bash
sudo tee /etc/supervisor/conf.d/sigrak-staging.conf > /dev/null <<'EOF'
[program:sigrak-staging-horizon]
command=/usr/bin/php /var/www/sigrak-staging/current/artisan horizon
user=sigrak-staging
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/sigrak-staging/horizon.log
stopwaitsecs=600

[program:sigrak-staging-scheduler]
command=/usr/bin/php /var/www/sigrak-staging/current/artisan schedule:work
user=sigrak-staging
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/sigrak-staging/scheduler.log
stopwaitsecs=60
EOF
sudo supervisorctl reread && sudo supervisorctl update
```

In staging's `config/horizon.php` environment (`staging`), set 1 process per queue.

Deploy: a copy of the section 10 script with `BASE=/var/www/sigrak-staging` and `sudo /usr/bin/supervisorctl restart sigrak-staging-scheduler`, run as `sigrak-staging`. A sudoers line like the production one, for `sigrak-staging`, allows the FPM reload and that restart. Add `/var/log/sigrak-staging/*.log` to the logrotate rule in section 13.

**Data rule.** Staging holds seeded, made-up data only. **Never copy production data into `sigrak_staging`**: production holds student names and teachers' encrypted AI keys. The team enters its own test Gemini keys through the staging app.

## Decisions to record

To be numbered during integration.

1. **PHP 8.5** (≥ 8.5.12) from `ppa:ondrej/php`, with PHP 8.4 as fallback only if a required package blocks 8.5. Reason: Laravel 13 supports 8.3–8.5, and 8.5 has the longest support window.
2. **MySQL 8.4 LTS** from Oracle's APT repository (`mysql-8.4-lts`), not Ubuntu's 8.0 (end of life) and not 9.7 LTS (D-007 says MySQL 8).
3. **Redis 8.2** (extended release) from packages.redis.io, pinned to `6:8.2.*`, with `noeviction` and AOF on.
4. **Gotenberg image `gotenberg/gotenberg:8.39.0-chromium`**: the Chromium-only variant, published on `127.0.0.1:3000` with basic auth and a 2 GB memory limit, on its own Docker network whose new outbound connections are dropped; fonts mounted from `/opt/gotenberg/fonts`.
5. **Node 24 LTS for builds only**, preferably in CI; no Node on the server unless needed.
6. **Ubuntu's Nginx package** (1.24 on 24.04, patched by Canonical) rather than nginx.org builds, listening on `127.0.0.1:8080` only, plain HTTP behind the tunnel.
7. **No public IP; Cloudflare Tunnel for all traffic** (owner, 2026-10-31): a locally-managed `cloudflared` tunnel per server routes `app.`, `ujian.`, `admin.`, and `ssh.` to localhost. Real IP from `CF-Connecting-IP` (trusted only from 127.0.0.1). UFW denies all incoming. SSH and `admin.` sit behind Cloudflare Access. This replaces the Origin CA / Authenticated Origin Pulls / Cloudflare IP allow-list design.
8. **Three Nginx vhosts for one Laravel codebase** (`app.`, `ujian.`, `admin.`), as in `architecture.md`, with host-only session cookies (no shared `SESSION_DOMAIN`).
9. **Release-folder deploys** (`releases/`, `shared/`, `current` symlink), keeping 5 releases; PHP-FPM reload on each deploy because OPcache does not check timestamps.
10. **Supervisor runs Horizon and `schedule:work`** (no cron entry for the scheduler; `architecture.md` shows the equivalent cron form); worker counts live in `config/horizon.php`.
11. **Backups:** daily 01:30 WIB (18:30 UTC) `mysqldump` + `storage/app` tarball, 14 days local, 30 days off-site through an encrypted `rclone` remote; monthly restore test, off-site restore twice a year. `APP_KEY` kept in the password manager, never in the backup.
12. **Server, PHP, and MySQL run in UTC**; the app displays `Asia/Jakarta` (as in `architecture.md`). Cron times in this guide are UTC.
13. **Domain `sigrak.com`** with hostnames `app.`, `ujian.`, `admin.` (owner, 2026-10-31).
14. **VPS provider: IDCloudHost Cloud VPS, Jakarta** (owner, 2026-10-31).
15. **Off-site backups on Cloudflare R2**, bucket-scoped Object Read & Write token, encrypted with an `rclone` crypt remote (owner, 2026-10-31).
16. **Staging is a second site on the production VPS** (owner, 2026-10-31). It has its own user, FPM pool, DB and DB user, Redis prefixes, and Horizon/scheduler programs. Its `staging-*` hostnames run on the same tunnel behind Cloudflare Access. MySQL, Redis, Gotenberg, Nginx, and `cloudflared` are shared, so only app releases are staged. Seeded data only.
17. **Gotenberg `.env` keys** follow `spatie/laravel-pdf` v2: `LARAVEL_PDF_DRIVER=gotenberg`, `GOTENBERG_URL`, `GOTENBERG_USERNAME`, `GOTENBERG_PASSWORD`.
18. **Server monitoring is a separate task** (owner, 2026-10-31); not covered in this guide.
19. **Ubuntu Server 24.04 LTS** instead of 22.04 (owner, 2026-10-31; D-086).
20. **Production VPS: IDCloudHost custom package, 8 vCPU / 16 GB RAM** (owner, 2026-10-31; D-087); sizing in sections 3–5, 7, and 11 follows it.
21. **Production disk: 320 GB NVMe** (owner, 2026-10-31).

## Open questions

- [ ] Confirm with IDCloudHost that a VM without a public IP has outbound internet (NAT), including TCP/UDP 7844 to Cloudflare.
- [ ] `sigrak.com` is chosen; the PDKI trademark check in `product-concept.md` still applies to the name.

Answered by the owner on 2026-10-31: R2 for off-site backups, IDCloudHost Jakarta, domain `sigrak.com`, staging required, Gotenberg `.env` keys set now (decision 17), monitoring as a separate task, Ubuntu 24.04 LTS, 8 vCPU / 16 GB custom package, Cloudflare Tunnel because the VPS has no public IP, 320 GB production disk, staging as a second site on the same VPS.
