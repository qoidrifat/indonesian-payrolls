<?php

namespace App\Services\Reporting;

use App\Models\PayrollItem;
use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PayslipGenerator
{
    public function generate(PayrollItem $item, string $disk = 'local'): Payslip
    {
        $item->loadMissing(['employee.profile', 'employee.taxProfile', 'run.period', 'taxCalculation', 'bpjsCalculation']);

        $period      = $item->run->period;
        $payslipNum  = $this->payslipNumber($item);
        $relativeDir = "payslips/{$period->code}";
        $relative    = "{$relativeDir}/{$payslipNum}.pdf";

        $pdf = Pdf::loadView('payslip.standard', [
            'item'   => $item,
            'period' => $period,
            'number' => $payslipNum,
        ]);

        Storage::disk($disk)->makeDirectory($relativeDir);
        Storage::disk($disk)->put($relative, $pdf->output());

        return Payslip::updateOrCreate(
            ['payroll_item_id' => $item->id],
            [
                'employee_id'        => $item->employee_id,
                'payroll_period_id'  => $period->id,
                'payslip_number'     => $payslipNum,
                'pdf_path'           => $relative,
                'pdf_disk'           => $disk,
                'snapshot'           => $item->breakdown,
                'generated_at'       => now(),
            ]
        );
    }

    private function payslipNumber(PayrollItem $item): string
    {
        return sprintf(
            'PS-%s-%06d',
            $item->run->period->code,
            $item->id,
        );
    }
}
