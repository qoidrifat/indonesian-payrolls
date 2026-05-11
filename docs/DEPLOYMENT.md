# Deployment Guide

Aurex is a standard Laravel 12 + Inertia + Vue 3 application — it deploys anywhere PHP 8.3 + MySQL 8 + Node 20 run. This guide covers three common targets.

---

## 0. Prerequisites

- PHP 8.3+ with extensions: `mbstring`, `xml`, `bcmath`, `gd`, `intl`, `mysql`, `zip`, `curl`, `pdo_mysql`, `tokenizer`, `fileinfo`
- Composer 2
- Node 20 + npm
- MySQL 8 (or MariaDB 10.6+)
- Writable `storage/` and `bootstrap/cache/` (mode 775)

Recommended `.env` baseline (production):

```ini
APP_NAME="Aurex Payroll"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://payroll.example.com
APP_TIMEZONE=Asia/Jakarta

LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aurex_payroll
DB_USERNAME=aurex
DB_PASSWORD=change-me

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 1. VPS (Ubuntu 24.04 + Nginx + PHP-FPM)

### 1.1 Install runtime

```bash
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y nginx mysql-server \
    php8.3-fpm php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-gd \
    php8.3-intl php8.3-mysql php8.3-zip php8.3-curl php8.3-cli \
    php8.3-tokenizer php8.3-fileinfo unzip git
curl -sLS https://deb.nodesource.com/setup_20.x | sudo bash -
sudo apt install -y nodejs
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### 1.2 Create database

```bash
sudo mysql <<'SQL'
CREATE DATABASE aurex_payroll CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'aurex'@'localhost' IDENTIFIED BY 'change-me';
GRANT ALL PRIVILEGES ON aurex_payroll.* TO 'aurex'@'localhost';
FLUSH PRIVILEGES;
SQL
```

### 1.3 Clone & build

```bash
sudo mkdir -p /var/www && cd /var/www
sudo git clone https://github.com/qoidrifat/indonesian-payrolls.git aurex
cd aurex
sudo cp .env.example .env       # then edit /var/www/aurex/.env

sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm ci
sudo -u www-data npm run build

sudo -u www-data php artisan key:generate
sudo -u www-data php artisan migrate --force --seed
sudo -u www-data php artisan storage:link
sudo -u www-data php artisan optimize

sudo chown -R www-data:www-data /var/www/aurex
sudo chmod -R 775 storage bootstrap/cache
```

### 1.4 Nginx vhost

`/etc/nginx/sites-available/aurex`:

```nginx
server {
    listen 80;
    server_name payroll.example.com;
    root /var/www/aurex/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

Enable + reload:

```bash
sudo ln -s /etc/nginx/sites-available/aurex /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### 1.5 Add HTTPS

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d payroll.example.com
```

### 1.6 Scheduler & queue (optional)

`/etc/cron.d/aurex`:

```cron
* * * * * www-data cd /var/www/aurex && php artisan schedule:run >> /dev/null 2>&1
```

For queue workers (if you start using async jobs):

```bash
sudo apt install -y supervisor
```

`/etc/supervisor/conf.d/aurex-worker.conf`:

```ini
[program:aurex-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/aurex/artisan queue:work --tries=3 --max-time=3600
user=www-data
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/aurex-worker.log
```

```bash
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start aurex-worker:*
```

---

## 2. cPanel / shared hosting

Most cPanel hosts support PHP 8.3 + MySQL 8. Aurex deploys with the standard "Laravel via subdirectory" recipe:

### 2.1 Upload code

Either:
- Use cPanel's **Git Version Control** to clone the repo into `~/aurex` (NOT inside `public_html`), or
- SCP/SFTP the project into `~/aurex`.

### 2.2 Configure document root

cPanel → **Domains** → set the domain document root to `~/aurex/public`. If your host doesn't allow that, create a symlink:

```bash
cd ~/public_html
ln -s ~/aurex/public aurex
```

…and visit `https://yourdomain.com/aurex`.

### 2.3 Install dependencies

In **Terminal** (cPanel) — or, if SSH is disabled, use the **Composer / Node** managers:

```bash
cd ~/aurex
composer install --no-dev --optimize-autoloader
npm ci && npm run build
cp .env.example .env
php artisan key:generate
# edit ~/aurex/.env in File Manager: DB credentials from cPanel → MySQL Databases
php artisan migrate --force --seed
php artisan storage:link
php artisan optimize
```

If `php artisan storage:link` fails (some hosts disable symlinks), use **File Manager** to manually create a symlink: `public/storage` → `~/aurex/storage/app/public`.

### 2.4 Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

### 2.5 Scheduler

In cPanel → **Cron Jobs**, add:

```
* * * * * cd $HOME/aurex && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

---

## 3. Simple "git pull" deploy

For ongoing iteration:

```bash
cd /var/www/aurex
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize:clear && php artisan optimize
```

Optional: add a `.deploy.sh` script and a deploy key + GitHub action.

---

## 4. Health & smoke test

After deploy:

1. Visit `/up` — should return 200 (Laravel health endpoint).
2. Visit `/login`, sign in with `admin@aurex.test` / `password` (only if seeded).
3. Open the dashboard, scroll all sections, then create a new payroll for the current month, run calculation, and verify the line items.
4. Download a payslip PDF and a monthly payroll Excel report.

---

## 5. Troubleshooting

| Symptom                                     | Likely cause                                       | Fix                                                            |
| ------------------------------------------- | -------------------------------------------------- | -------------------------------------------------------------- |
| `500` + "No application encryption key set" | `APP_KEY` missing                                  | `php artisan key:generate --force`                             |
| Vite "manifest" error                       | Frontend not built                                 | `npm ci && npm run build`                                      |
| `SQLSTATE 1071 key too long`                | Old MySQL collation                                | Use MySQL 8 / set `Schema::defaultStringLength(191)`           |
| Payslip PDF blank                           | DomPDF storage permissions                         | `chmod -R 775 storage/framework/cache storage/app/payslips`    |
| Dark mode flickers on first load            | localStorage init script removed                   | Verify `resources/views/app.blade.php` `<script>` is present   |
| Login loops back                            | `SESSION_DRIVER` mis-configured                    | Use `database` and run `php artisan session:table && migrate`  |
