<?php

namespace App\Services\Reporting;

use App\Models\PayrollRun;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayrollSummaryExporter implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly PayrollRun $run)
    {
    }

    public function collection()
    {
        return $this->run->items()->with(['employee', 'taxCalculation', 'bpjsCalculation'])->get();
    }

    public function headings(): array
    {
        return [
            'NIK', 'Nama', 'Departemen', 'Jabatan',
            'Gaji Pokok', 'Tunjangan Tetap', 'Tunjangan Tidak Tetap', 'Lembur', 'THR', 'Bonus',
            'Bruto',
            'JHT', 'JP', 'Kesehatan', 'PPh21', 'Potongan Lain',
            'Total Potongan', 'Take Home Pay',
        ];
    }

    public function map($row): array
    {
        $bpjs = $row->bpjsCalculation;
        return [
            $row->employee?->employee_number,
            $row->employee?->full_name,
            $row->employee?->department?->name,
            $row->employee?->position?->name,
            (float) $row->basic_salary,
            (float) $row->fixed_allowance,
            (float) $row->variable_allowance,
            (float) $row->overtime_amount,
            (float) $row->thr_amount,
            (float) $row->bonus_amount,
            (float) $row->gross_salary,
            (float) ($bpjs?->jht_employee ?? 0),
            (float) ($bpjs?->jp_employee ?? 0),
            (float) ($bpjs?->kesehatan_employee ?? 0),
            (float) $row->pph21_amount,
            (float) $row->other_deductions,
            (float) $row->total_deductions,
            (float) $row->net_salary,
        ];
    }
}
