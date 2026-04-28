<?php

namespace App\Services\Payroll;

use App\DTO\BpjsResultDTO;
use App\DTO\TaxResultDTO;
use App\Models\Employee;
use App\Services\Tax\PtkpResolver;
use App\Services\Tax\TerTable;

/**
 * Monthly PPh 21 (Jan–Nov) using TER per PP 58/2023 + PMK 168/2023.
 *
 * Formula:
 *   gross_taxable     = sum of taxable earnings (incl. natura employer benefits if config says so)
 *   monthly PPh 21    = gross_taxable × TER(category, gross_taxable)
 *   non-NPWP surchage = +20%
 */
class Pph21TerCalculator
{
    public function __construct(
        private readonly TerTable $terTable,
        private readonly PtkpResolver $ptkp,
    ) {
    }

    public function calculate(
        Employee $employee,
        float $grossMonthly,
        BpjsResultDTO $bpjs,
    ): TaxResultDTO {
        $taxProfile  = $employee->taxProfile;
        $ptkpStatus  = $taxProfile?->ptkp_status ?? 'TK/0';
        $ptkpAmount  = $this->ptkp->amountFor($ptkpStatus);
        $terCategory = $taxProfile?->ter_category ?: $this->ptkp->terCategoryFor($ptkpStatus);

        $taxableGross = $grossMonthly;
        if (config('payroll.bpjs.add_employer_to_taxable_income')) {
            $taxableGross += $bpjs->taxableEmployerBenefits();
        }
        $taxableGross = round($taxableGross, 2);

        $rate     = $this->terTable->rateFor($terCategory, $taxableGross);
        $pph21    = $taxableGross * $rate;

        $hasNpwp = (bool) ($taxProfile?->has_npwp);
        if (! $hasNpwp) {
            $pph21 *= 1.20;
        }

        return new TaxResultDTO(
            method:                  'ter',
            terCategory:             $terCategory,
            ptkpStatus:              $ptkpStatus,
            ptkpAmount:              $ptkpAmount,
            grossTaxable:            $taxableGross,
            biayaJabatan:            0.0,
            jhtJpEmployee:           round($bpjs->jhtEmployee + $bpjs->jpEmployee, 2),
            netTaxable:              0.0,
            pkp:                     0.0,
            terRate:                 $rate,
            pph21Amount:             round($pph21, 2),
            nonNpwpSurchargeApplied: ! $hasNpwp,
        );
    }
}
