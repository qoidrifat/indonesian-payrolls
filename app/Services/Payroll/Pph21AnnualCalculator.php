<?php

namespace App\Services\Payroll;

use App\DTO\BpjsResultDTO;
use App\DTO\TaxResultDTO;
use App\Models\Employee;
use App\Services\Tax\ProgressiveTaxTable;
use App\Services\Tax\PtkpResolver;

/**
 * December annual reconciliation per UU HPP (UU 7/2021) Article 17 and PER-16/PJ/2016.
 *
 *   1. annual_gross    = ytd_gross + december_gross + employer natura benefits
 *   2. biaya_jabatan   = min(5% × annual_gross, 6,000,000)
 *   3. jht_jp_yearly   = ytd JHT+JP employee + december JHT+JP employee
 *   4. net_annual      = annual_gross − biaya_jabatan − jht_jp_yearly
 *   5. pkp             = floor((net_annual − PTKP) / 1000) × 1000   (rounded down to thousand)
 *   6. annual_pph21    = progressive(pkp)
 *   7. december_pph21  = annual_pph21 − ytd_pph21
 *
 * Non-NPWP surcharge: +20% on the *annual* tax for the period before the employee obtained NPWP.
 * Implementation here applies the surcharge if the employee currently has no NPWP.
 */
class Pph21AnnualCalculator
{
    public const BIAYA_JABATAN_RATE = 0.05;

    public const BIAYA_JABATAN_CAP_ANNUAL = 6_000_000.0;

    public function __construct(
        private readonly ProgressiveTaxTable $progressive,
        private readonly PtkpResolver $ptkp,
    ) {
    }

    public function calculate(
        Employee $employee,
        float $decemberGross,
        BpjsResultDTO $decemberBpjs,
        float $ytdGross,
        float $ytdJhtJpEmployee,
        float $ytdPph21,
    ): TaxResultDTO {
        $taxProfile = $employee->taxProfile;
        $ptkpStatus = $taxProfile?->ptkp_status ?? 'TK/0';
        $ptkpAmount = $this->ptkp->amountFor($ptkpStatus);

        $natura = config('payroll.bpjs.add_employer_to_taxable_income')
            ? $decemberBpjs->taxableEmployerBenefits()
            : 0.0;

        $annualGross   = $ytdGross + $decemberGross + $natura;
        $biayaJabatan  = min(self::BIAYA_JABATAN_RATE * $annualGross, self::BIAYA_JABATAN_CAP_ANNUAL);
        $jhtJpAnnual   = $ytdJhtJpEmployee + $decemberBpjs->jhtEmployee + $decemberBpjs->jpEmployee;
        $netAnnual     = max($annualGross - $biayaJabatan - $jhtJpAnnual, 0.0);
        $pkpRaw        = max($netAnnual - $ptkpAmount, 0.0);
        $pkp           = floor($pkpRaw / 1000) * 1000;

        $annualTax = $this->progressive->calculate($pkp);

        $hasNpwp = (bool) ($taxProfile?->has_npwp);
        if (! $hasNpwp) {
            $annualTax *= 1.20;
        }

        $decemberPph21 = max($annualTax - $ytdPph21, 0.0);

        return new TaxResultDTO(
            method:                  'progressive',
            terCategory:             null,
            ptkpStatus:              $ptkpStatus,
            ptkpAmount:              $ptkpAmount,
            grossTaxable:            round($decemberGross + $natura, 2),
            biayaJabatan:            round($biayaJabatan, 2),
            jhtJpEmployee:           round($jhtJpAnnual, 2),
            netTaxable:              round($netAnnual, 2),
            pkp:                     round($pkp, 2),
            terRate:                 null,
            pph21Amount:             round($decemberPph21, 2),
            nonNpwpSurchargeApplied: ! $hasNpwp,
            ytdGross:                round($ytdGross, 2),
            ytdPph21:                round($ytdPph21, 2),
        );
    }
}
