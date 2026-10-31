# Server Setup Guide

- **Status:** draft
- **Task:** P2-05
- **Last updated:** 2026-10-31 (owner answers: domain, VPS, R2, staging)
- **Versions checked online on 2026-10-30** (sources in [Versions](#versions)).

## Purpose

A step-by-step guide to prepare the production VPS for SIGRAK (IDCloudHost Cloud VPS, Jakarta) and the staging VPS: the main app, the Filament back office, the TKA exam site, queue workers, and the Gotenberg PDF service. It is for whoever sets up or rebuilds the server. Run the sections in order. Every command is meant to be copied as-is. Replace only the placeholders below; never commit real values.

| Placeholder | Meaning |
|---|---|
| `app.sigrak.com` | School app |
| `ujian.sigrak.com` | TKA exam site (D-027) |
| `admin.sigrak.com` | Staff console (Filament) + Horizon |
| `<SERVER_IP>` | Public IP of the VPS |
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
| OS | Ubuntu Server 22.04 LTS | CONTEXT.md | Standard support ends mid-2027 (Canonical lists Jun 2027); see Open questions | ubuntu.com list of releases |
| Laravel | 13.x | Current major (13.0 on 2026-03-17) | Bug fixes to Q3 2027, security to 2028-03-17 | laravel.com/docs/13.x/releases |
| PHP | **8.5** (≥ 8.5.12) from `ppa:ondrej/php` | Laravel 13 supports 8.3–8.5; 8.5 has active support to 2027-12-31, security to 2029-12-31. 8.5.12 fixes the Oct 2026 CVEs | php.net/supported-versions | php.net, launchpad.net/~ondrej |
| Nginx | Ubuntu package (1.18 + Ubuntu security patches) | Fewer moving parts; patched by Canonical | Follows OS | Ubuntu archive |
| MySQL | **8.4 LTS** (8.4.13 on 2026-10-20) from repo.mysql.com, component `mysql-8.4-lts` | MySQL 8 per D-007; 8.4 is the 8.x LTS line | Premier support to Apr 2029 | dev.mysql.com release notes, endoflife.date/mysql |
| Redis | **8.2.x** from packages.redis.io, pinned `6:8.2.*` (8.2.9 for jammy) | 8.2 is an extended release (5-year fixes); 8.10 is newest but short-lived | To 2030-09-01 | redis.io, packages.redis.io |
| Horizon | latest major via Composer | Queue dashboard + supervisors on Redis | — | laravel.com/docs/13.x/horizon |
| Filament | 5.x | PHP 8.2+, Laravel 11.28+ | — | filamentphp.com/docs/5.x |
| Laravel Excel | `maatwebsite/excel` 4.x | 4.x supports Laravel 12/13 and PHP 8.5 (3.1 blocks PHP 8.5 via PhpSpreadsheet 1.x) | — | packagist.org/packages/maatwebsite/excel |
| Docker Engine | docker-ce from download.docker.com | Only for Gotenberg (D-028) | — | docs.docker.com |
| Gotenberg | **`gotenberg/gotenberg:8.39.0-chromium`** (released 2026-10-27) | Chromium-only variant: no LibreOffice needed (no Word-to-PDF, D-031), ~30% smaller | — | github.com/gotenberg/gotenberg/releases, Docker Hub |
| Node.js | **24 LTS** (24.22.2), build only | Maintenance LTS to 2028-04-30, mature with Vite/Tailwind. Node 26 became LTS on 2026-10-28; revisit after it settles | — | nodejs.org releases, nodejs/Release schedule |
| Cloudflare | Free plan features: proxy, Full (strict), Origin CA, Authenticated Origin Pulls | CONTEXT.md | — | developers.cloudflare.com |

## Content

### 1. Base system

Create the VM in the IDCloudHost console: **Cloud VPS, location Jakarta**, image Ubuntu Server 22.04 LTS (see Open questions about 24.04), at least 2 vCPU / 4 GB RAM / 80 GB NVMe disk (Chromium in Gotenberg and MySQL are the heaviest parts). Give it a public (floating) IP. IDCloudHost creates the first login with a username and password you choose; use a long random password, then log in and switch to root (`sudo -i`) once:

```bash
apt update && apt -y full-upgrade
apt -y install curl gnupg ca-certificates lsb-release software-properties-common \
  unzip git acl ufw fail2ban unattended-upgrades supervisor logrotate
timedatectl set-timezone Etc/UTC   # the app displays Asia/Jakarta; cron times below are UTC
hostnamectl set-hostname sigrak-prod
dpkg-reconfigure -plow unattended-upgrades   # answer "Yes"
```

Swap (2 GB), so a burst of PDF jobs does not trigger the OOM killer:

```bash
fallocate -l 2G /swapfile && chmod 600 /swapfile && mkswap /swapfile && swapon /swapfile
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

SSH hardening. **Before** running this, open a second terminal and confirm `ssh admin@<SERVER_IP>` works with your key.

```bash
cat > /etc/ssh/sshd_config.d/99-sigrak.conf <<'EOF'
PermitRootLogin no
PasswordAuthentication no
KbdInteractiveAuthentication no
EOF
sshd -t && systemctl reload ssh
```

fail2ban ships with the `sshd` jail enabled on Ubuntu; check it with `fail2ban-client status sshd`.

From here on, work as `admin` with `sudo`.

### 2. Firewall (UFW)

Only SSH from anywhere; HTTP/HTTPS only from Cloudflare (section 9 adds those rules). Nothing else is exposed: MySQL, Redis, and Gotenberg listen on localhost only.

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw limit OpenSSH
sudo ufw enable
sudo ufw status verbose
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
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 500
request_terminate_timeout = 65s
EOF
sudo mv /etc/php/8.5/fpm/pool.d/www.conf /etc/php/8.5/fpm/pool.d/www.conf.disabled
sudo systemctl restart php8.5-fpm && sudo systemctl enable php8.5-fpm
```

`pm.max_children` ≈ (RAM left for PHP) ÷ (average worker size, about 60–80 MB). 20 fits a 4 GB VPS that also runs MySQL, Redis, and Gotenberg. Check with `ps -o rss= -C php-fpm8.5 | sort -n | tail -3`.

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

Ubuntu 22.04 ships MySQL 8.0 (end of life since April 2026), so use Oracle's APT repository with the `mysql-8.4-lts` component, which never jumps to 9.x by itself.

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
innodb_buffer_pool_size = 1G
max_connections = 100
slow_query_log = ON
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 1
EOF
sudo systemctl restart mysql && sudo systemctl enable mysql
```

`innodb_buffer_pool_size`: about 25–30% of RAM on this shared box (1G for 4 GB).

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

Ubuntu 22.04 ships Redis 6.0, so use the official repository and pin the 8.2 line:

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
maxmemory 512mb
maxmemory-policy noeviction
appendonly yes
appendfsync everysec
EOF
sudo systemctl restart redis-server && sudo systemctl enable redis-server
redis-cli -a '<REDIS_PASSWORD>' --no-auth-warning ping    # PONG
```

Redis 8 is licensed AGPLv3/RSALv2/SSPLv1. We run it unmodified as an internal service, which none of these restrict.

### 6. Nginx

```bash
sudo apt -y install nginx
sudo rm -f /etc/nginx/sites-enabled/default
sudo sed -i 's/# server_tokens off;/server_tokens off;/' /etc/nginx/nginx.conf
```

TLS files (created in section 9): `/etc/ssl/cloudflare/origin.pem`, `/etc/ssl/cloudflare/origin.key`, `/etc/ssl/cloudflare/aop-ca.pem`.

Shared snippet for both vhosts:

```bash
sudo tee /etc/nginx/snippets/sigrak-common.conf > /dev/null <<'EOF'
root /var/www/sigrak/current/public;
index index.php;
charset utf-8;

ssl_certificate     /etc/ssl/cloudflare/origin.pem;
ssl_certificate_key /etc/ssl/cloudflare/origin.key;
ssl_protocols TLSv1.2 TLSv1.3;
ssl_client_certificate /etc/ssl/cloudflare/aop-ca.pem;
ssl_verify_client on;

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
    listen 80;
    listen [::]:80;
    server_name app.sigrak.com ujian.sigrak.com admin.sigrak.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
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
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ujian.sigrak.com;
    client_max_body_size 1m;
    access_log /var/log/nginx/sigrak-ujian.access.log;
    error_log  /var/log/nginx/sigrak-ujian.error.log;
    include snippets/sigrak-common.conf;
}
EOF
```

Staff console vhost (`admin.sigrak.com`: Filament and Horizon behind the `staff` guard). To narrow it further, a Cloudflare Access policy or WAF rule on this hostname can allow only the team.

```bash
sudo tee /etc/nginx/sites-available/sigrak-admin.conf > /dev/null <<'EOF'
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name admin.sigrak.com;
    client_max_body_size 25m;
    access_log /var/log/nginx/sigrak-admin.access.log;
    error_log  /var/log/nginx/sigrak-admin.error.log;
    include snippets/sigrak-common.conf;
}
EOF
```

Catch-all that drops requests for any other host name (for example scans on the bare IP):

```bash
sudo tee /etc/nginx/sites-available/00-default.conf > /dev/null <<'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    listen 443 ssl http2 default_server;
    listen [::]:443 ssl http2 default_server;
    server_name _;
    ssl_certificate     /etc/ssl/cloudflare/origin.pem;
    ssl_certificate_key /etc/ssl/cloudflare/origin.key;
    return 444;
}
EOF
sudo ln -s /etc/nginx/sites-available/00-default.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/sigrak-app.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/sigrak-ujian.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/sigrak-admin.conf /etc/nginx/sites-enabled/
```

Run `sudo nginx -t && sudo systemctl reload nginx` only after section 9 has put the certificates and the real-IP file in place.

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
    mem_limit: 1g
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

### 9. Cloudflare

**DNS** (Cloudflare dashboard → DNS): `A app`, `A ujian`, and `A admin`, each pointing to `<SERVER_IP>` and **Proxied** (orange cloud). Add `AAAA` records too if the VPS has IPv6.

**SSL/TLS** (dashboard → SSL/TLS):

1. Origin Server → Create Certificate → RSA, hostnames `sigrak.com` and `*.sigrak.com`, validity 15 years. Save both parts on the server:

   ```bash
   sudo mkdir -p /etc/ssl/cloudflare && sudo chmod 750 /etc/ssl/cloudflare
   sudo nano /etc/ssl/cloudflare/origin.pem    # paste the certificate
   sudo nano /etc/ssl/cloudflare/origin.key    # paste the private key
   sudo chmod 640 /etc/ssl/cloudflare/origin.* && sudo chgrp www-data /etc/ssl/cloudflare /etc/ssl/cloudflare/origin.*
   ```

2. Overview → encryption mode **Full (strict)**.
3. Edge Certificates → **Always Use HTTPS** on, **Minimum TLS Version** 1.2. Turn HSTS on only after a week without TLS problems.
4. Origin Server → **Authenticated Origin Pulls** on (global). Install Cloudflare's client CA so Nginx accepts only Cloudflare (the `ssl_verify_client on` line in the snippet):

   ```bash
   sudo curl -fsSL https://developers.cloudflare.com/ssl/static/authenticated_origin_pull_ca.pem -o /etc/ssl/cloudflare/aop-ca.pem
   ```

   The global certificate proves a request comes from Cloudflare, not from our account. Together with the UFW allow-list that is enough for v1; zone-level AOP with our own certificate is an option later.

**Real client IP.** Nginx must replace Cloudflare's address with the visitor's (`CF-Connecting-IP`), so Laravel logs, throttling, and exam audit trails see the real IP. The same list drives UFW. One script refreshes both:

```bash
sudo tee /usr/local/sbin/cloudflare-refresh > /dev/null <<'EOF'
#!/usr/bin/env bash
set -euo pipefail
V4="$(curl -fsS https://www.cloudflare.com/ips-v4)"
V6="$(curl -fsS https://www.cloudflare.com/ips-v6)"
COUNT="$(printf '%s\n%s\n' "$V4" "$V6" | grep -c '/')"
if [ "$COUNT" -lt 10 ]; then echo "Cloudflare IP list looks wrong ($COUNT entries), keeping old config"; exit 1; fi

TMP="$(mktemp)"
{
  echo "# Generated $(date -Iseconds) from https://www.cloudflare.com/ips"
  for ip in $V4 $V6; do echo "set_real_ip_from $ip;"; done
  echo "real_ip_header CF-Connecting-IP;"
} > "$TMP"
install -m 644 "$TMP" /etc/nginx/conf.d/cloudflare-realip.conf
rm -f "$TMP"

for ip in $V4 $V6; do
  ufw allow proto tcp from "$ip" to any port 80,443 comment cloudflare > /dev/null
done

nginx -t && systemctl reload nginx
EOF
sudo chmod 755 /usr/local/sbin/cloudflare-refresh
sudo /usr/local/sbin/cloudflare-refresh
echo '17 4 * * 1 root /usr/local/sbin/cloudflare-refresh >> /var/log/cloudflare-refresh.log 2>&1' | sudo tee /etc/cron.d/cloudflare-refresh
sudo ufw status numbered | grep -c cloudflare   # one line per range
```

The script only adds UFW rules. If Cloudflare ever removes a range, delete its rule by hand (`sudo ufw status numbered`, then `sudo ufw delete <n>`).

**Things Cloudflare changes for the app:**

- Proxied requests time out after 100 seconds. AI generation and PDF export already run as queued jobs; no web request may wait for them.
- Free plan uploads are capped at 100 MB per request (ours are far below).
- Leave "I'm Under Attack" mode and aggressive bot rules **off** for `ujian.sigrak.com`: a class signs in at the same moment from one IP, which looks like an attack.
- Cloudflare does not cache HTML by default; keep it that way. Only `/build/*` is cached long-term.

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

Starting point for `config/horizon.php` on a 4 GB VPS (tune after real load):

| Supervisor | Queue | Processes | Timeout |
|---|---|---|---|
| `supervisor-ai` | `ai` | 3–6 (auto balance) | 330 s |
| `supervisor-exports` | `exports` | 2 | 180 s |
| `supervisor-default` | `default` | 2 | 60 s |

Exports call Gotenberg; two at a time fits the 1 GB container limit.

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

Full disaster restore: rebuild a VPS with sections 1–11, restore the dump into `sigrak`, extract `storage/app` into `shared/storage/`, put `.env` back with the **original** `APP_KEY`, deploy, then move the Cloudflare DNS records to the new IP.

### 13. Log rotation

| Log | Rotation |
|---|---|
| Laravel (`storage/logs/laravel-YYYY-MM-DD.log`) | By Laravel itself: `LOG_STACK=daily`, `LOG_DAILY_DAYS=14` |
| Nginx, MySQL, Redis, fail2ban, UFW | Ubuntu's packaged logrotate rules (keep them) |
| `/var/log/sigrak/*.log` (Horizon, scheduler, backup) | Rule below |
| Docker (Gotenberg) | `daemon.json`: 3 × 10 MB per container |
| systemd journal | Capped below |

```bash
sudo tee /etc/logrotate.d/sigrak > /dev/null <<'EOF'
/var/log/sigrak/*.log /var/log/cloudflare-refresh.log {
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
sudo ufw status | grep -E '80,443|OpenSSH'
sudo supervisorctl status                          # horizon + scheduler RUNNING
curl -sI https://app.sigrak.com | head -1          # through Cloudflare: 200 or 302 to login
curl -sI https://admin.sigrak.com | head -1        # staff login page
curl -skI --resolve app.sigrak.com:443:<SERVER_IP> https://app.sigrak.com | head -1   # direct: must fail (UFW/AOP)
```

In the app: log in, create one document, export it to PDF, and check in Horizon that the `ai` and `exports` queues ran the jobs. Open `ujian.sigrak.com` on a phone and check that it shows the exam sign-in and nothing from the main app. Check that Horizon opens only on `admin.sigrak.com` after staff login, and that Telescope is not reachable.

### 15. Staging server

Staging is a **second, smaller IDCloudHost Cloud VPS in Jakarta** (2 vCPU / 2–4 GB RAM / 40 GB), built with sections 1–14 above. Every release and every version bump (PHP, MySQL, Redis, Gotenberg image) goes to staging first.

Differences from production:

| Item | Staging |
|---|---|
| Hostnames | `staging-app.sigrak.com`, `staging-ujian.sigrak.com`, `staging-admin.sigrak.com` (one level deep, so the same `*.sigrak.com` Origin CA certificate covers them) |
| Nginx | Same three vhost files with these `server_name`s, plus `add_header X-Robots-Tag "noindex, nofollow" always;` in each |
| Access | Cloudflare Zero Trust → Access → Application for `staging-*.sigrak.com`, allowing only the team's emails, so staging is never public |
| `.env` | `APP_ENV=staging`, `APP_URL=https://staging-app.sigrak.com`, its own `APP_KEY`, DB/Redis/Gotenberg passwords, `MAIL_MAILER=log`, `LOG_LEVEL=debug` |
| Telescope | Allowed: `TELESCOPE_ENABLED=true`, visible only to staff (architecture.md) |
| Data | Seeded and made-up data only. **Never copy production data to staging**: it holds student names and teachers' encrypted AI keys |
| AI keys | The team's own test Gemini keys, entered through the app |
| Backups | Database only, local 7 days; no R2 upload (`rclone` lines removed from the backup script) |
| Size | `pm.max_children = 8`, `innodb_buffer_pool_size = 512M`, Redis `maxmemory 256mb`, Horizon 1 process per queue |

DNS: `A staging-app`, `A staging-ujian`, and `A staging-admin` → staging server IP, all **Proxied**.

## Decisions to record

To be numbered during integration.

1. **PHP 8.5** (≥ 8.5.12) from `ppa:ondrej/php`, with PHP 8.4 as fallback only if a required package blocks 8.5. Reason: Laravel 13 supports 8.3–8.5, and 8.5 has the longest support window.
2. **MySQL 8.4 LTS** from Oracle's APT repository (`mysql-8.4-lts`), not Ubuntu's 8.0 (end of life) and not 9.7 LTS (D-007 says MySQL 8).
3. **Redis 8.2** (extended release) from packages.redis.io, pinned to `6:8.2.*`, with `noeviction` and AOF on.
4. **Gotenberg image `gotenberg/gotenberg:8.39.0-chromium`**: the Chromium-only variant, published on `127.0.0.1:3000` with basic auth and a 1 GB memory limit, on its own Docker network whose new outbound connections are dropped; fonts mounted from `/opt/gotenberg/fonts`.
5. **Node 24 LTS for builds only**, preferably in CI; no Node on the server unless needed.
6. **Ubuntu's Nginx package** (1.18, patched by Canonical) rather than nginx.org builds.
7. **Cloudflare: Full (strict) with a 15-year Origin CA wildcard certificate + global Authenticated Origin Pulls**; UFW allows 80/443 only from Cloudflare ranges, refreshed weekly together with the Nginx real-IP list.
8. **Three Nginx vhosts for one Laravel codebase** (`app.`, `ujian.`, `admin.`), as in `architecture.md`, with host-only session cookies (no shared `SESSION_DOMAIN`).
9. **Release-folder deploys** (`releases/`, `shared/`, `current` symlink), keeping 5 releases; PHP-FPM reload on each deploy because OPcache does not check timestamps.
10. **Supervisor runs Horizon and `schedule:work`** (no cron entry for the scheduler; `architecture.md` shows the equivalent cron form); worker counts live in `config/horizon.php`.
11. **Backups:** daily 01:30 WIB (18:30 UTC) `mysqldump` + `storage/app` tarball, 14 days local, 30 days off-site through an encrypted `rclone` remote; monthly restore test, off-site restore twice a year. `APP_KEY` kept in the password manager, never in the backup.
12. **Server, PHP, and MySQL run in UTC**; the app displays `Asia/Jakarta` (as in `architecture.md`). Cron times in this guide are UTC.
13. **Domain `sigrak.com`** with hostnames `app.`, `ujian.`, `admin.` (owner, 2026-10-31).
14. **VPS provider: IDCloudHost Cloud VPS, Jakarta** (owner, 2026-10-31).
15. **Off-site backups on Cloudflare R2**, bucket-scoped Object Read & Write token, encrypted with an `rclone` crypt remote (owner, 2026-10-31).
16. **A staging server is required** (owner, 2026-10-31): a second, smaller IDCloudHost VPS in Jakarta on `staging-*.sigrak.com`, behind Cloudflare Access, seeded data only.
17. **Gotenberg `.env` keys** follow `spatie/laravel-pdf` v2: `LARAVEL_PDF_DRIVER=gotenberg`, `GOTENBERG_URL`, `GOTENBERG_USERNAME`, `GOTENBERG_PASSWORD`.
18. **Server monitoring is a separate task** (owner, 2026-10-31); not covered in this guide.

## Open questions

- [ ] **Ubuntu 22.04 reaches end of standard support in mid-2027** (Canonical lists June 2027; some sources say May 2027). The TKA practice season runs January–March 2027, so the server would need an OS upgrade soon after launch. Options: (a) start on **Ubuntu 24.04 LTS** instead (supported to 2029; every repository used here supports it, and the commands use `$(lsb_release -cs)`, so they work unchanged), or (b) stay on 22.04 and attach Ubuntu Pro for security updates to 2032. Recommendation: (a). Needs an owner decision, because CONTEXT.md says 22.04.
- [ ] Exact IDCloudHost plan (CPU/RAM/disk) for production and staging, after checking current prices.
- [ ] `sigrak.com` is chosen; the PDKI trademark check in `product-concept.md` still applies to the name.

Answered by the owner on 2026-10-31: R2 for off-site backups, IDCloudHost Jakarta, domain `sigrak.com`, staging required, Gotenberg `.env` keys set now (decision 17), monitoring as a separate task.
