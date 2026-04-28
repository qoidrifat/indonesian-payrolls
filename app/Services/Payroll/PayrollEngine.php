<?php

namespace App\Services\Payroll;

use App\DTO\PayrollContextDTO;
use App\DTO\PayrollResultDTO;

/**
 * Orchestrates the per-employee payroll calculation. Composes the BPJS calculator
 * and the appropriate PPh 21 calculator (TER for Jan–Nov, progressive for Dec).
 *
 * Pure-domain: this class does NOT touch Eloquent. The workflow service is
 * responsible for persistence (see PayrollWorkflow).
 */
class PayrollEngine
{
    public function __construct(
        private readonly BpjsCalculator $bpjs,
        private readonly Pph21TerCalculator $pph21Ter,
        private readonly Pph21AnnualCalculator $pph21Annual,
    ) {
    }

    public function calculate(
        PayrollContextDTO $ctx,
        float $ytdGross = 0.0,
        float $ytdJhtJpEmployee = 0.0,
        float $ytdPph21 = 0.0,
    ): PayrollResultDTO {
        $bpjs = $this->bpjs->calculate($ctx->bpjsBaseWage(), $ctx->employee);

        $tax = $ctx->isDecemberReconciliation
            ? $this->pph21Annual->calculate(
                $ctx->employee,
                $ctx->grossSalary(),
                $bpjs,
                $ytdGross,
                $ytdJhtJpEmployee,
                $ytdPph21,
            )
            : $this->pph21Ter->calculate(
                $ctx->employee,
                $ctx->grossSalary(),
                $bpjs,
            );

        $totalDeductions = round(
            $bpjs->totalEmployee() + $tax->pph21Amount + $ctx->otherDeductions,
            2
        );

        $netSalary = round($ctx->grossSalary() - $totalDeductions, 2);

        return new PayrollResultDTO(
            context:         $ctx,
            bpjs:            $bpjs,
            tax:             $tax,
            grossSalary:     $ctx->grossSalary(),
            totalDeductions: $totalDeductions,
            netSalary:       $netSalary,
        );
    }
}
