<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Payslip {{ $number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #222; margin: 0; }
        .container { padding: 24px; }
        h1 { font-size: 16px; margin: 0 0 8px; }
        h2 { font-size: 13px; margin: 16px 0 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { padding: 4px 8px; }
        th { text-align: left; background: #f4f4f4; }
        .right { text-align: right; }
        .meta td { vertical-align: top; }
        .total td { font-weight: bold; border-top: 1px solid #999; }
        .net { background: #efe; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h1>Slip Gaji / Payslip</h1>

    <table class="meta">
        <tr>
            <td>No. Slip</td><td>: {{ $number }}</td>
            <td>Periode</td><td>: {{ $period->code }}</td>
        </tr>
        <tr>
            <td>NIK</td><td>: {{ $item->employee->employee_number }}</td>
            <td>Nama</td><td>: {{ $item->employee->full_name }}</td>
        </tr>
        <tr>
            <td>Departemen</td><td>: {{ $item->employee->department?->name }}</td>
            <td>Jabatan</td><td>: {{ $item->employee->position?->name }}</td>
        </tr>
        <tr>
            <td>NPWP</td><td>: {{ $item->employee->taxProfile?->npwp_number ?? '-' }}</td>
            <td>PTKP</td><td>: {{ $item->employee->taxProfile?->ptkp_status ?? '-' }}</td>
        </tr>
    </table>

    <h2>Penghasilan (Earnings)</h2>
    <table>
        <tr><th>Komponen</th><th class="right">Jumlah (IDR)</th></tr>
        <tr><td>Gaji Pokok</td><td class="right">{{ number_format((float) $item->basic_salary, 0, ',', '.') }}</td></tr>
        <tr><td>Tunjangan Tetap</td><td class="right">{{ number_format((float) $item->fixed_allowance, 0, ',', '.') }}</td></tr>
        <tr><td>Tunjangan Tidak Tetap</td><td class="right">{{ number_format((float) $item->variable_allowance, 0, ',', '.') }}</td></tr>
        <tr><td>Lembur</td><td class="right">{{ number_format((float) $item->overtime_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Bonus / Insentif</td><td class="right">{{ number_format((float) $item->bonus_amount, 0, ',', '.') }}</td></tr>
        <tr><td>THR</td><td class="right">{{ number_format((float) $item->thr_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Lainnya</td><td class="right">{{ number_format((float) $item->other_earnings, 0, ',', '.') }}</td></tr>
        <tr class="total"><td>Bruto / Gross</td><td class="right">{{ number_format((float) $item->gross_salary, 0, ',', '.') }}</td></tr>
    </table>

    <h2>Potongan (Deductions)</h2>
    <table>
        <tr><th>Komponen</th><th class="right">Jumlah (IDR)</th></tr>
        @php($bpjs = $item->bpjsCalculation)
        @if($bpjs)
            <tr><td>BPJS JHT (2%)</td><td class="right">{{ number_format((float) $bpjs->jht_employee, 0, ',', '.') }}</td></tr>
            <tr><td>BPJS JP (1%)</td><td class="right">{{ number_format((float) $bpjs->jp_employee, 0, ',', '.') }}</td></tr>
            <tr><td>BPJS Kesehatan (1%)</td><td class="right">{{ number_format((float) $bpjs->kesehatan_employee, 0, ',', '.') }}</td></tr>
        @endif
        <tr><td>PPh 21 ({{ $item->taxCalculation?->method === 'ter' ? 'TER ' . number_format(($item->taxCalculation?->ter_rate ?? 0) * 100, 2) . '%' : 'Progresif' }})</td><td class="right">{{ number_format((float) $item->pph21_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Potongan Lainnya</td><td class="right">{{ number_format((float) $item->other_deductions, 0, ',', '.') }}</td></tr>
        <tr class="total"><td>Total Potongan</td><td class="right">{{ number_format((float) $item->total_deductions, 0, ',', '.') }}</td></tr>
    </table>

    <h2>Take Home Pay</h2>
    <table>
        <tr class="net"><td>NET / Diterima</td><td class="right">IDR {{ number_format((float) $item->net_salary, 0, ',', '.') }}</td></tr>
    </table>

    <p style="font-size: 10px; color: #666; margin-top: 24px;">
        Slip ini dibuat secara elektronik dan sah tanpa tanda tangan basah. PPh 21 dipotong sesuai PMK 168/2023 (skema TER) untuk Januari–November dan rekonsiliasi tahunan UU HPP untuk Desember.
    </p>
</div>
</body>
</html>
