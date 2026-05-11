<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payroll {{ $payroll->code }}</title>
    <style>
        @page { margin: 22px 26px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #111827; }
        .header { background: linear-gradient(120deg, #4f46e5, #7c3aed); color: white; padding: 14px 18px; border-radius: 10px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 16px; }
        .header .meta { font-size: 10px; opacity: .9; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px 7px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; text-transform: uppercase; font-size: 9px; letter-spacing: .5px; color: #6b7280; text-align: left; }
        td.num { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        tfoot td { font-weight: 700; background: #f9fafb; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }} — Payroll {{ $payroll->code }}</h1>
        <div class="meta">Period: {{ $payroll->periodLabel() }} · {{ $payroll->items->count() }} employees · Status: {{ ucfirst($payroll->status) }}</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Position</th>
                <th class="num">Base</th>
                <th class="num">Allowances</th>
                <th class="num">OT + Bonus</th>
                <th class="num">Gross</th>
                <th class="num">BPJS</th>
                <th class="num">PPh 21</th>
                <th class="num">Net</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payroll->items as $i)
                <tr>
                    <td>{{ $i->employee->employee_code }}</td>
                    <td>{{ $i->employee->name }}</td>
                    <td>{{ $i->employee->position }}</td>
                    <td class="num">{{ number_format($i->base_salary, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->allowances, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->overtime_pay + $i->bonus, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->gross_salary, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->bpjs_kesehatan_employee + $i->bpjs_jht_employee + $i->bpjs_jp_employee, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->pph21, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($i->net_salary, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align:right;">Totals</td>
                <td class="num">{{ number_format($payroll->total_gross, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
                <td class="num">{{ number_format($payroll->total_net, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
