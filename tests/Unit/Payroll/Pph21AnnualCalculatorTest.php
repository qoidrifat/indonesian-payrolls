<?php

use App\Models\Employee;
use App\Models\TaxProfile;
use App\Services\Payroll\BpjsCalculator;
use App\Services\Payroll\Pph21AnnualCalculator;
use App\Services\Tax\ProgressiveTaxTable;
use App\Services\Tax\PtkpResolver;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('reconciles December PPh 21 against the year-to-date amount per UU HPP', function () {
    // Test config: ignore natura employer benefits to keep arithmetic clean.
    config(['payroll.bpjs.add_employer_to_taxable_income' => false]);

    $employee = Employee::factory()->create();
    TaxProfile::factory()->for($employee)->create([
        'has_npwp'    => true,
        'ptkp_status' => 'TK/0',
    ]);

    $bpjsCalc = new BpjsCalculator();
    // Full-year 10M/month, BPJS computed each month identically
    $monthlyBpjs = $bpjsCalc->calculate(10_000_000, $employee->fresh(['taxProfile', 'bpjsProfile']));

    // Jan–Nov: TER A 10,000,000 → bracket 9,650,000–10,050,000 → 2.00%
    $monthlyPph21 = round(10_000_000 * 0.02, 2); // 200,000
    $ytdGross     = 10_000_000 * 11;             // 110,000,000
    $ytdJhtJp     = ($monthlyBpjs->jhtEmployee + $monthlyBpjs->jpEmployee) * 11;
    $ytdPph21     = $monthlyPph21 * 11;          // 2,200,000

    $calc = new Pph21AnnualCalculator(new ProgressiveTaxTable(), new PtkpResolver());
    $result = $calc->calculate(
        employee:           $employee->fresh('taxProfile'),
        decemberGross:      10_000_000,
        decemberBpjs:       $monthlyBpjs,
        ytdGross:           $ytdGross,
        ytdJhtJpEmployee:   $ytdJhtJp,
        ytdPph21:           $ytdPph21,
    );

    // annual_gross    = 120,000,000
    // biaya_jabatan   = min(5% × 120M, 6M) = 6,000,000
    // jht_jp annual   = (200k + 100k) × 12 = 3,600,000
    // net_annual      = 110,400,000
    // PTKP TK/0       = 54,000,000
    // PKP             = 56,400,000 (round down to thousand)
    // annual_tax      = 5% × 56,400,000 = 2,820,000
    // dec_pph21       = 2,820,000 - 2,200,000 = 620,000
    expect($result->method)->toBe('progressive')
        ->and($result->ptkpAmount)->toBe(54_000_000.0)
        ->and($result->biayaJabatan)->toBe(6_000_000.0)
        ->and($result->pkp)->toBe(56_400_000.0)
        ->and($result->pph21Amount)->toBe(620_000.0);
});
