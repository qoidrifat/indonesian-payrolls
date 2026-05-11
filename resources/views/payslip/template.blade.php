<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Payslip {{ $number }}</title>
    <style>
        @page { margin: 24px 28px; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111827; }
        .header { background: linear-gradient(120deg, #4f46e5, #7c3aed, #06b6d4); color: white; padding: 16px 20px; border-radius: 12px; margin-bottom: 18px; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 700; letter-spacing: .3px; }
        .header .meta { font-size: 10px; opacity: .9; margin-top: 4px; }
        .grid { display: table; width: 100%; }
        .col { display: table-cell; vertical-align: top; padding-right: 12px; }
        .col-right { text-align: right; padding-right: 0; }
        .card-block { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; margin-bottom: 12px; }
        .h-section { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #6366f1; font-weight: 700; margin-bottom: 6px; }
        table.table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .table th, .table td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; text-align: left; }
        .table th { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
        .table td.num { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        .net-band { background: linear-gradient(120deg, rgba(99,102,241,.08), rgba(124,58,237,.08), rgba(6,182,212,.08)); border: 1px solid rgba(99,102,241,.18); border-radius: 12px; padding: 14px 16px; margin-top: 14px; }
        .net-band .label { font-size: 11px; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .net-band .value { font-size: 22px; font-weight: 700; color: #111827; margin-top: 4px; }
        .muted { color: #6b7280; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; background: rgba(99,102,241,.12); color: #4f46e5; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="grid">
            <div class="col">
                <h1>{{ $company['name'] }}</h1>
                <div class="meta">{{ $company['address'] }}</div>
            </div>
            <div class="col col-right">
                <div style="font-size:14px;font-weight:600;">Payslip</div>
                <div class="meta">{{ $number }}</div>
                <div class="meta">Issued {{ now()->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <div class="grid">
        <div class="col" style="width:50%;">
            <div class="card-block">
                <div class="h-section">Employee</div>
                <div style="font-weight:600;font-size:13px;">{{ $employee->name }}</div>
                <div class="muted">{{ $employee->position }}@if($employee->department) · {{ $employee->department }}@endif</div>
                <div class="muted">Code: {{ $employee->employee_code }}</div>
                <div class="muted">NPWP: {{ $employee->npwp ?? '—' }}</div>
                <div class="muted">PTKP: {{ $employee->marital_status }}</div>
            </div>
        </div>
        <div class="col" style="width:50%;">
            <div class="card-block">
                <div class="h-section">Pay period</div>
                <div style="font-weight:600;font-size:13px;">{{ $payroll->periodLabel() }}</div>
                <div class="muted">Code: {{ $payroll->code }}</div>
                <div class="muted">Working days: {{ $item->working_days }} · Present: {{ $item->present_days }}</div>
                <div class="muted">OT: {{ $item->overtime_minutes }} min</div>
                <div style="margin-top:6px;"><span class="badge">{{ ucfirst($payroll->status) }}</span></div>
            </div>
        </div>
    </div>

    <div class="card-block">
        <div class="h-section">Earnings</div>
        <table class="table">
            <thead>
                <tr><th>Item</th><th class="num" style="text-align:right;">Amount (IDR)</th></tr>
            </thead>
            <tbody>
                <tr><td>Base salary (prorated)</td><td class="num">{{ number_format($item->base_salary, 0, ',', '.') }}</td></tr>
                @foreach((array)($item->breakdown['allowances'] ?? []) as $a)
                    <tr><td>Allowance · {{ $a['name'] ?? 'Allowance' }}</td><td class="num">{{ number_format($a['amount'] ?? 0, 0, ',', '.') }}</td></tr>
                @endforeach
                <tr><td>Overtime</td><td class="num">{{ number_format($item->overtime_pay, 0, ',', '.') }}</td></tr>
                <tr><td>Bonus</td><td class="num">{{ number_format($item->bonus, 0, ',', '.') }}</td></tr>
            </tbody>
            <tfoot>
                <tr style="font-weight:700;">
                    <td>Gross salary</td>
                    <td class="num">{{ number_format($item->gross_salary, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="card-block">
        <div class="h-section">Deductions</div>
        <table class="table">
            <tbody>
                <tr><td>BPJS Kesehatan (1%)</td><td class="num">{{ number_format($item->bpjs_kesehatan_employee, 0, ',', '.') }}</td></tr>
                <tr><td>BPJS JHT (2%)</td><td class="num">{{ number_format($item->bpjs_jht_employee, 0, ',', '.') }}</td></tr>
                <tr><td>BPJS JP (1%)</td><td class="num">{{ number_format($item->bpjs_jp_employee, 0, ',', '.') }}</td></tr>
                <tr><td>PPh 21</td><td class="num">{{ number_format($item->pph21, 0, ',', '.') }}</td></tr>
                <tr><td>Other deductions</td><td class="num">{{ number_format($item->other_deductions, 0, ',', '.') }}</td></tr>
            </tbody>
            <tfoot>
                <tr style="font-weight:700;">
                    <td>Total deductions</td>
                    <td class="num">{{ number_format($item->total_deductions, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="net-band">
        <div class="grid">
            <div class="col">
                <div class="label">Take home pay</div>
                <div class="value">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</div>
            </div>
            <div class="col col-right" style="vertical-align:middle;">
                <div class="muted">Paid via {{ $employee->bank_name ?? '—' }}</div>
                <div class="muted">{{ $employee->bank_account_number ?? '' }}</div>
                <div class="muted">{{ $employee->bank_account_name ?? '' }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        This payslip is system-generated by {{ $company['name'] }}. Calculations follow PMK 168/2023 (TER), UU HPP, and BPJS Perpres 64/2020.
    </div>
</body>
</html>
