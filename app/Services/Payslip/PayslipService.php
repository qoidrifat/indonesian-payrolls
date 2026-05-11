<?php

namespace App\Services\Payslip;

use App\Models\PayrollItem;
use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PayslipService
{
    public function generate(PayrollItem $item): Payslip
    {
        $item->loadMissing('employee', 'payroll');

        $payslip = Payslip::firstOrNew(
            ['payroll_item_id' => $item->id],
            ['employee_id' => $item->employee_id]
        );
        if (! $payslip->exists) {
            $payslip->number = $this->makeNumber($item);
        }

        $pdf = Pdf::loadView('payslip.template', [
            'item' => $item,
            'employee' => $item->employee,
            'payroll' => $item->payroll,
            'company' => [
                'name' => config('app.name'),
                'address' => 'Kabupaten Bangkalan, Jawa Timur, Indonesia',
            ],
            'number' => $payslip->number ?? $this->makeNumber($item),
        ])->setPaper('a4');

        $relative = sprintf(
            'payslips/%d/%s.pdf',
            $item->payroll_id,
            Str::slug($item->employee->employee_code).'-'.$item->employee_id,
        );
        Storage::disk('local')->put($relative, $pdf->output());

        $payslip->fill([
            'file_path' => $relative,
            'issued_at' => now(),
            'number' => $payslip->number,
        ])->save();

        return $payslip;
    }

    public function stream(PayrollItem $item)
    {
        $item->loadMissing('employee', 'payroll');

        return Pdf::loadView('payslip.template', [
            'item' => $item,
            'employee' => $item->employee,
            'payroll' => $item->payroll,
            'company' => [
                'name' => config('app.name'),
                'address' => 'Kabupaten Bangkalan, Jawa Timur, Indonesia',
            ],
            'number' => $this->makeNumber($item),
        ])->setPaper('a4');
    }

    private function makeNumber(PayrollItem $item): string
    {
        return sprintf(
            'PSL/%04d%02d/%s/%05d',
            $item->payroll->period_year,
            $item->payroll->period_month,
            strtoupper($item->employee->employee_code),
            $item->id,
        );
    }
}
