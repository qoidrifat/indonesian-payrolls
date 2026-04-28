# API Reference

All routes are versioned under `/api/v1` and protected by Sanctum (Bearer token) except for `POST /auth/login`.

## Authentication

```
POST /api/v1/auth/login
{
  "email": "admin@example.test",
  "password": "password"
}

→ 200
{
  "token": "1|abc...",
  "user":  { "id": 1, "name": "...", "email": "..." }
}
```

Use the token as `Authorization: Bearer <token>`.

```
POST /api/v1/auth/logout
GET  /api/v1/me
```

## Employees

```
GET  /api/v1/employees                  # paginated
GET  /api/v1/employees/{id}             # with relations
```

## Payroll periods

```
GET  /api/v1/payroll-periods
GET  /api/v1/payroll-periods/{id}
```

## Payroll runs

```
GET   /api/v1/payroll-runs                   # paginated
POST  /api/v1/payroll-runs                   # create draft
      { "payroll_period_id": 1, "name": "Jun 2024", "run_type": "monthly" }
GET   /api/v1/payroll-runs/{id}              # with items + employees
POST  /api/v1/payroll-runs/{id}/calculate    # synchronous calc
POST  /api/v1/payroll-runs/{id}/approve      # finance approval
POST  /api/v1/payroll-runs/{id}/mark-paid    # after disbursement
```

## Payslips

```
GET  /api/v1/payslips/{id}                   # JSON
GET  /api/v1/payslips/{id}/pdf               # streamed PDF
```

## Rate limits

- `POST /api/v1/auth/login` — 5 requests / minute / IP
- All other endpoints — `throttle:api` (60 / min default)
