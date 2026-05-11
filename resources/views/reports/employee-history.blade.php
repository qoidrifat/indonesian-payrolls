<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary history — {{ $employee->name }}</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111827; }
        .header { background: linear-gradient(120deg, #4f46e5, #7c3aed); color: white; padding: 14px 18px; border-radius: 10px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 16px; }
        .header .meta { font-size: 10px; opacity: .9; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; text-transform: uppercase; font-size: 9px; letter-spacing: .5px; color: #6b7280; text-align: left; }
        td.num { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $employee->name }} · Salary history</h1>
        <div class="meta">{{ $employee->position }} · {{ $employee->employee_code }}</div>
    </div>
    <table>
        <thead>
            <tr><th>Period</th><th>Code</th><th class="num">Gross</th><th class="num">BPJS</th><th class="num">PPh 21</th><th class="num">Net</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach($employee->payrollItems as $i)
                <tr>
                    <td>{{ $i->payroll->periodLabel() }}</td>
                    <td>{{ $i->payroll->code }}</td>
                    <td class="num">{{ number_format($i->gross_salary, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->bpjs_kesehatan_employee + $i->bpjs_jht_employee + $i->bpjs_jp_employee, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->pph21, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->net_salary, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($i->payroll->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
