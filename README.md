# Aurex — Modern Indonesian Payroll SaaS

Aurex is a lightweight but production-ready payroll management system built for small Indonesian IT & web companies (~10 people). It is intentionally simple, opinionated, and beautiful — modeled after the UX of modern SaaS products like Linear, Stripe Dashboard, and Vercel.

> Built for [Kabupaten Bangkalan, Jawa Timur](https://en.wikipedia.org/wiki/Bangkalan_Regency) — but works for any small Indonesian team.

---

## Stack

| Layer           | Technology                                |
| --------------- | ----------------------------------------- |
| Backend         | Laravel 12 · PHP 8.3                      |
| Frontend        | Vue 3 · InertiaJS · TailwindCSS           |
| Database        | MySQL 8 (SQLite for testing)              |
| Auth            | Laravel Breeze (Inertia)                  |
| Authorization   | Spatie Laravel Permission                 |
| PDF             | DomPDF (`barryvdh/laravel-dompdf`)        |
| Excel           | Laravel Excel (Maatwebsite)               |
| Activity Logs   | Spatie Laravel Activity Log               |
| Charts          | ApexCharts (vue3-apexcharts)              |
| Icons           | Heroicons                                 |
| Typography      | Plus Jakarta Sans · Inter · JetBrains Mono |

## Features

1. **Authentication** — Breeze-powered login, register, password reset, email verification, with a polished split-screen Aurex visual identity.
2. **Dashboard** — Live payroll, headcount, attendance, and activity stats with animated charts.
3. **Employee Management** — Full CRUD over employees with NIK, NPWP, BPJS, bank info, PTKP status, employment status.
4. **Attendance Tracking** — Manual entry of present / remote / leave / sick / absent / holiday with check-in/out & overtime minutes.
5. **Salary Configuration** — Per-employee recurring allowances, deductions, and bonuses with `is_taxable` and `is_recurring` flags.
6. **Payroll Processing** — Draft → Calculate → Approve → Pay workflow with full audit log and re-runnable calculations.
7. **Payslip Generator** — Beautiful gradient-accented PDF payslips with PMK 168/2023-compliant tax breakdown.
8. **Payroll History** — Browse historical runs with full line-item drill-down.
9. **Reports** — Monthly payroll (PDF + Excel), tax summary, attendance summary, per-employee salary history.
10. **Settings** — Company profile, payroll defaults, BPJS rate configuration.

## Compliance highlights

- **PPh 21**: PMK 168/2023 TER (Tarif Efektif Rata-rata) table with 43 brackets adjusted per PTKP status (TK / K0 / K1 / K2 / K3). 20% penalty applied for employees without NPWP per UU HPP.
- **BPJS Kesehatan**: 1% employee / 4% company on base salary, capped at IDR 12,000,000 base per Perpres 64/2020.
- **BPJS JHT**: 2% employee / 3.7% company.
- **BPJS JP**: 1% employee / 2% company, capped at the JP ceiling (≈ IDR 10,042,300 base).
- **Overtime**: PP 35/2021 simplified — first hour at 1.5× hourly rate, subsequent hours at 2.0×. Hourly rate computed as monthly / 173.

> Note: e-Bupot 21 XML export and WLKP annual report generators are stubbed for a future release.

---

## Quick start

```bash
git clone https://github.com/qoidrifat/indonesian-payrolls.git
cd indonesian-payrolls

cp .env.example .env
composer install
npm install
php artisan key:generate

# configure DB credentials in .env, then:
php artisan migrate --seed
php artisan storage:link

# dev (two terminals or use concurrently):
npm run dev
php artisan serve
```

Visit http://localhost:8000.

### Seeded demo data

`php artisan migrate:fresh --seed` creates:

- 1 admin user — `admin@aurex.test` / `password`
- 10 realistic employees (Tech Lead, Backend, Frontend, Fullstack, DevOps, Designer, PM, QA, HR Lead, Operations)
- Mixed PTKP statuses (TK/K0/K1/K2) for tax-bracket testing
- Salary components (transport allowance + leadership allowance)
- Last month's payroll fully calculated and marked **paid** (≈ Rp 111.6 M net)
- Month-to-date attendance entries (present/remote/sick mix)

---

## Architecture

```
app/
├── Actions/
│   └── Payroll/RunPayroll.php          # idempotent payroll calculation orchestrator
├── Exports/                            # Maatwebsite Excel exports
├── Http/Controllers/                    # thin controllers, one per resource
├── Models/                              # Employee, Attendance, Payroll, PayrollItem, Payslip, SalaryComponent, Setting
└── Services/
    ├── Payroll/
    │   ├── PayrollEngine.php            # combines BPJS + PPh21 + overtime + bonus
    │   ├── BpjsCalculator.php           # 1% / 2% / 1% employee · 4% / 3.7% / 2% company
    │   ├── Pph21Calculator.php          # PMK 168/2023 TER 43-bracket table + PTKP
    │   ├── OvertimeCalculator.php       # PP 35/2021 (1.5x / 2.0x)
    │   └── DTOs/{PayrollInput,PayrollResult}.php
    └── Payslip/PayslipService.php       # DomPDF rendering of payslip.template.blade.php

resources/
├── js/
│   ├── Layouts/
│   │   ├── AuthenticatedLayout.vue      # sidebar + topbar + dark-mode toggle
│   │   └── GuestLayout.vue              # split-screen auth layout
│   ├── Components/                      # StatCard, ChartCard, Modal, Pagination, etc.
│   ├── Pages/{Dashboard,Employees,Attendance,Payroll,Payslips,Reports,Settings,Auth,Profile,Welcome}.vue
│   └── lib/format.js                    # IDR & date formatters
├── css/app.css                          # Tailwind layers + glass / btn-primary / chips
└── views/
    ├── app.blade.php                    # Inertia root
    ├── payslip/template.blade.php       # DomPDF template
    └── reports/{payroll,employee-history}.blade.php
```

### Payroll workflow

```
draft  →  calculated  →  approved  →  paid
   ↘         ↑                      ↘
    delete   re-run                  cancelled
```

- **draft**: created, no items yet.
- **calculated**: `RunPayroll` action computed line items for all active employees.
- **approved**: finance lead has signed off; items frozen.
- **paid**: bank transfers done, `pay_date` recorded.

Re-running calculation is idempotent — existing items are wiped & replaced in a transaction.

---

## Testing

```bash
# Unit + feature suite (SQLite :memory:)
php artisan test

# Just the payroll math:
php artisan test --testsuite=Unit
```

Coverage focus: BPJS rates, PPh 21 TER brackets, no-NPWP penalty, pro-ration, overtime, and the end-to-end Draft→Calculated workflow.

---

## Lint & build

```bash
./vendor/bin/pint                # PHP style (Laravel Pint)
npm run build                    # production assets
```

---

## Deployment

See [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) for cPanel, shared hosting, and VPS step-by-step.

Quick sketch (VPS, Nginx + PHP-FPM + MySQL 8):

```bash
git clone <repo> /var/www/aurex && cd /var/www/aurex
cp .env.example .env             # configure APP_*, DB_*, MAIL_*, etc.
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize
chown -R www-data:www-data storage bootstrap/cache
```

Nginx points `root` at `/var/www/aurex/public`. PHP-FPM 8.3.

Run the scheduler:

```cron
* * * * * cd /var/www/aurex && php artisan schedule:run >> /dev/null 2>&1
```

---

## License

MIT.
