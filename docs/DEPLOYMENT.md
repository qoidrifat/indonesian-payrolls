# Deployment Guide

## Quick start (Docker Compose)

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

The admin panel is reachable at `http://localhost:8080/admin` once `nginx` is up.
Default admin login (from the demo seeder):

```
Email:    admin@example.test
Password: password
```

## Production deployment

This repo is designed to deploy onto:

- **Compute**: 2× Ubuntu 22+ EC2 / Droplet (web) + 1× worker (Horizon).
- **DB**: MySQL 8 (RDS or DigitalOcean Managed DB).
- **Cache/Queue**: Redis 7 (ElastiCache or DigitalOcean Managed Redis).
- **Storage**: S3 (or any S3-compatible object store) for payslip PDFs — switch `FILESYSTEM_DISK=s3`.

### Required environment variables

| Variable | Notes |
|---|---|
| `APP_KEY` | `php artisan key:generate --show` |
| `APP_URL` | Public URL |
| `DB_*` | MySQL connection |
| `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD` | Redis |
| `QUEUE_CONNECTION=redis` | Required for Horizon |
| `CACHE_STORE=redis`, `SESSION_DRIVER=redis` | Recommended |
| `FILESYSTEM_DISK=s3` | For payslip PDFs |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` | S3 |
| `MAIL_*` | For payslip email notifications |

### Horizon

The `horizon` service runs `php artisan horizon` under supervisord and also runs
the scheduler loop. Configure in `config/horizon.php`. Dashboard at `/horizon`.

### Scheduler

Scheduled tasks (defined in `routes/console.php`):

- `payroll:close-period` — 1st of every month at 03:00 WIB
- `payroll:thr-trigger`  — 30 days before declared religious holiday
- `payroll:export-bpjs`  — monthly

### Backups

Recommend `spatie/laravel-backup` (not yet installed) to back up MySQL + storage to S3 nightly.

### Hardening checklist

- [ ] Run behind TLS (Let's Encrypt or AWS ACM)
- [ ] Restrict `/horizon` and `/admin` by IP allowlist or SSO
- [ ] Configure `app.encrypt_cookies = true`
- [ ] Enable database encryption at rest (RDS option)
- [ ] Rotate `APP_KEY` only via `php artisan key:rotate` (never re-encrypt without it)
- [ ] Set `LOG_CHANNEL=stack` with both `daily` and `papertrail` or `cloudwatch`
- [ ] Run database migrations with `php artisan migrate --force` in CI/CD; never `migrate:fresh` in prod

## CI

`.github/workflows/ci.yml` runs Pest on PHP 8.3 against an in-memory SQLite DB
on every push/PR. Lint runs in non-blocking mode.
