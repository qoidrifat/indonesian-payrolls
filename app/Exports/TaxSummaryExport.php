<?php

namespace App\Exports;

use App\Models\PayrollItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TaxSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    public function __construct(public int $year) {}

    public function collection(): Collection
    {
        return PayrollItem::query()
            ->join('payrolls', 'payrolls.id', '=', 'payroll_items.payroll_id')
            ->join('employees', 'employees.id', '=', 'payroll_items.employee_id')
            ->where('payrolls.period_year', $this->year)
            ->selectRaw('employees.employee_code, employees.name, employees.npwp, '.
                'SUM(payroll_items.gross_salary) as gross, SUM(payroll_items.pph21) as pph21')
            ->groupBy('employees.id', 'employees.employee_code', 'employees.name', 'employees.npwp')
            ->orderBy('employees.name')
            ->get();
    }

    public function headings(): array
    {
        return ['Code', 'Name', 'NPWP', 'Total Gross', 'Total PPh 21'];
    }

    public function map($row): array
    {
        return [$row->employee_code, $row->name, $row->npwp ?? '-', (float) $row->gross, (float) $row->pph21];
    }

    public function title(): string
    {
        return 'Tax '.$this->year;
    }
}
