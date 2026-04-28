# Indonesian Payroll System

Production-grade payroll engine for Indonesian companies, compliant with:

- **PMK 168/2023** — TER (Tarif Efektif Rata-rata) for monthly PPh 21 (Jan–Nov)
- **UU HPP / UU 7/2021** — progressive Article 17 brackets for the December reconciliation
- **PMK 101/2016** — PTKP (non-taxable income)
- **PP 35/2021** — overtime
- **PP 44/2015 / PP 45/2015 / PP 46/2015** — BPJS Ketenagakerjaan (JHT, JP, JKK, JKM)
- **Perpres 64/2020** — BPJS Kesehatan
- **PP 36/2021** — wage components, THR

Built on **Laravel 12**, **PHP 8.3**, **MySQL 8**, **Filament v4**, **Redis + Horizon**.

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Open http://127.0.0.1:8000/admin and log in:

```
admin@example.test / password
```

## What's implemented

- ✅ Full database schema (22 tables, with FK + indexes)
- ✅ Pure-domain payroll engine with DTOs (`app/Services/Payroll/`)
- ✅ BPJS calculator (5 programs, salary caps, JKK risk grades)
- ✅ PPh 21 TER (monthly) + progressive (December reconciliation)
- ✅ Overtime calculator per PP 35/2021 (workday + rest day, 5/6-day workweek)
- ✅ Payroll workflow state machine (draft → calculated → approved → paid)
- ✅ Idempotent per-employee calculation jobs (Horizon-ready)
- ✅ Filament v4 admin (Employee, Department, Position, SalaryStructure, PayrollPeriod, PayrollRun, Payslip)
- ✅ RBAC: super_admin / hr / finance / employee
- ✅ Audit log via spatie/activitylog
- ✅ Sanctum REST API (`/api/v1/*`)
- ✅ Payslip PDF generator (DomPDF) + Excel summary export
- ✅ Pest unit + feature tests (13 passing)
- ✅ Docker Compose stack (PHP-FPM, Nginx, MySQL, Redis, Horizon)
- ✅ GitHub Actions CI

## What's stubbed for follow-up

These are scaffolded but intentionally lightweight; flesh them out as needed:

- e-Bupot 21 XML exporter (DJP-compliant)
- Employee self-service portal (Inertia + Vue)
- THR auto-trigger by religious holiday calendar
- WLKP annual report generator
- Multi-tenant sharding for >100k employees

## Documentation

- [`docs/DESIGN.md`](docs/DESIGN.md) — full design doc covering Phases 1–13 from the brief
- [`docs/API.md`](docs/API.md) — REST endpoint reference
- [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) — Docker + production deploy guide

## Running tests

```bash
./vendor/bin/pest
```

## License

MIT
