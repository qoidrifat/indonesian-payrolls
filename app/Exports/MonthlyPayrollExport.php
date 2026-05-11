<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class MonthlyPayrollExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    public function __construct(public Payroll $payroll) {}

    public function collection()
    {
        return $this->payroll->items()->with('employee')->get();
    }

    public function headings(): array
    {
        return [
            'Employee Code', 'Name', 'Position', 'Department',
            'Base Salary', 'Allowances', 'Overtime', 'Bonus',
            'Gross', 'BPJS Kes (Emp)', 'BPJS JHT (Emp)', 'BPJS JP (Emp)',
            'PPh 21', 'Other Deductions', 'Total Deductions', 'Net',
        ];
    }

    public function map($row): array
    {
        $e = $row->employee;

        return [
            $e->employee_code, $e->name, $e->position, $e->department,
            (float) $row->base_salary, (float) $row->allowances,
            (float) $row->overtime_pay, (float) $row->bonus,
            (float) $row->gross_salary,
            (float) $row->bpjs_kesehatan_employee,
            (float) $row->bpjs_jht_employee,
            (float) $row->bpjs_jp_employee,
            (float) $row->pph21, (float) $row->other_deductions,
            (float) $row->total_deductions, (float) $row->net_salary,
        ];
    }

    public function title(): string
    {
        return 'Payroll '.$this->payroll->code;
    }
}
