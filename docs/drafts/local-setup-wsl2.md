# Local development on Windows (WSL2)

How a developer runs SIGRAK on a Windows laptop before code goes to staging and production. Local development uses **WSL2 with Ubuntu 24.04**, not Laragon (D-166). Ubuntu 24.04 matches the VPS (D-086), so Horizon, `phpredis`, and `pcntl` work the same as on the server.

Versions follow [server-setup.md](../final/server-setup.md#versions); only the local differences are noted here.

## 1. WSL2 and Ubuntu

In PowerShell as Administrator:

```bash
wsl --install -d Ubuntu-24.04
```

Restart Windows, open "Ubuntu 24.04", and create the Linux user.

- Keep the code **inside Linux** (`~/sigrak`), never under `/mnt/c/...`. On `/mnt/c`, Composer, Vite, and the tests are several times slower and file watching is unreliable.
- Edit with VS Code and its **WSL** extension: run `code .` from the Ubuntu terminal inside the project.

## 2. PHP, MySQL, Redis

```bash
sudo LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php && sudo apt update
```

```bash
sudo apt -y install php8.5-cli php8.5-mysql php8.5-redis php8.5-mbstring php8.5-xml php8.5-curl php8.5-zip php8.5-gd php8.5-intl php8.5-bcmath mysql-server redis-server unzip git
```

- No PHP-FPM or Nginx locally: `php artisan serve` is enough.
- Ubuntu's `mysql-server` is 8.0; production and CI use 8.4. That is fine for local work; CI catches version differences.
- Ubuntu's Redis is older than production's 8.2. Also fine locally.

Install Composer with the official installer from getcomposer.org.

## 3. Node.js 24 LTS

Install with `nvm` (github.com/nvm-sh/nvm), then:

```bash
nvm install 24
```

## 4. Database

MySQL on Ubuntu listens on `127.0.0.1` only, so a local user with `'%'` is not reachable from outside. `testing` is the database the Pest suite uses (`phpunit.xml`).

```bash
sudo mysql -e "CREATE DATABASE sigrak; CREATE DATABASE testing; CREATE USER 'sigrak'@'%' IDENTIFIED BY 'secret'; GRANT ALL ON sigrak.* TO 'sigrak'@'%'; GRANT ALL ON testing.* TO 'sigrak'@'%';"
```

## 5. Gotenberg (PDF)

Install **Docker Desktop** on Windows and turn on *Settings → Resources → WSL integration* for Ubuntu 24.04. Then, in Ubuntu:

```bash
docker run -d --name gotenberg --restart unless-stopped -p 127.0.0.1:3000:3000 gotenberg/gotenberg:8.39.0-chromium
```

Locally Gotenberg runs without basic auth or the network lock-down from D-059.

## 6. `.env`

```bash
cp .env.example .env
```

Fill in at least these values. The three hostnames use `.localhost`: Chrome and Edge send every `*.localhost` name to the local machine, so the Windows hosts file needs no edits.

```
APP_ENV=local
APP_DEBUG=true
APP_URL=http://app.sigrak.localhost:8000
APP_HOST=app.sigrak.localhost
EXAM_HOST=ujian.sigrak.localhost
ADMIN_HOST=admin.sigrak.localhost

LOG_STACK=single
LOG_LEVEL=debug

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sigrak
DB_USERNAME=sigrak
DB_PASSWORD=secret

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
SESSION_SECURE_COOKIE=false

TELESCOPE_ENABLED=true

LARAVEL_PDF_DRIVER=gotenberg
GOTENBERG_URL=http://127.0.0.1:3000
```

Tests read `DB_HOST`, `DB_USERNAME`, and `DB_PASSWORD` from `.env` and use the `testing` database.

## 7. First run

```bash
composer install && npm ci
```

```bash
php artisan key:generate && php artisan migrate --seed
```

## 8. Daily work

```bash
composer dev
```

`composer dev` runs `php artisan dev`, which starts `serve` (port 8000), `queue:listen`, `pail` (logs), and Vite together. Open:

- School app: `http://app.sigrak.localhost:8000`
- Exam site: `http://ujian.sigrak.localhost:8000`
- Admin (Filament, Horizon, Telescope): `http://admin.sigrak.localhost:8000`

`queue:listen` only works the `default` queue. To try Horizon or the named queues from `config/horizon.php`, run `php artisan horizon` in a second terminal.

Before a push, run the checks CI runs:

```bash
vendor/bin/pint --test && vendor/bin/phpstan analyse --memory-limit=1G && composer test
```

```bash
npm run lint && npm run check && npm run build
```

## Troubleshooting

- **The page loads without CSS/JS:** open `public/hot`. If it holds `[::1]`, start Vite with `npm run dev -- --host 127.0.0.1` (Windows does not always forward IPv6 into WSL).
- **`localhost:8000` does not open from Windows:** check that WSL's localhost forwarding is on (default; `localhostForwarding=true` in `%UserProfile%\.wslconfig`), then run `wsl --shutdown` and reopen Ubuntu.
- **Slow `composer install` or `npm ci`:** the project is under `/mnt/c`. Move it to `~/`.
- **`docker: command not found` in Ubuntu:** turn on WSL integration in Docker Desktop and reopen the terminal.
