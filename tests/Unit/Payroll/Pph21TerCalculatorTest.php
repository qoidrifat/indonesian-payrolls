<?php

use App\Models\Employee;
use App\Models\TaxProfile;
use App\Services\Payroll\BpjsCalculator;
use App\Services\Payroll\Pph21TerCalculator;
use App\Services\Tax\PtkpResolver;
use App\Services\Tax\TerTable;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('calculates monthly PPh 21 using TER for an employee with NPWP', function () {
    $employee = Employee::factory()->create();
    TaxProfile::factory()->for($employee)->create([
        'has_npwp'    => true,
        'ptkp_status' => 'TK/0',
        'ter_category' => 'A',
    ]);

    $bpjsCalc = new BpjsCalculator();
    $bpjs     = $bpjsCalc->calculate(8_000_000, $employee->fresh(['taxProfile', 'bpjsProfile']));

    $calc = new Pph21TerCalculator(new TerTable(), new PtkpResolver());
    $result = $calc->calculate($employee->fresh('taxProfile'), 8_000_000, $bpjs);

    // Gross taxable = 8,000,000 + employer benefits (kesehatan 320k + jkk 19,200 + jkm 24k = 363,200) = 8,363,200
    // TER A bracket [7,500,000–8,550,000) → 1.50%
    expect($result->terCategory)->toBe('A')
        ->and($result->grossTaxable)->toBe(8_363_200.0)
        ->and($result->terRate)->toBe(0.015)
        ->and($result->pph21Amount)->toBe(round(8_363_200 * 0.015, 2))
        ->and($result->nonNpwpSurchargeApplied)->toBeFalse();
});

it('applies a 20% surcharge for employees without NPWP', function () {
    $employee = Employee::factory()->create();
    TaxProfile::factory()->for($employee)->create([
        'has_npwp'     => false,
        'ptkp_status'  => 'TK/0',
        'ter_category' => 'A',
    ]);

    $bpjsCalc = new BpjsCalculator();
    $bpjs     = $bpjsCalc->calculate(8_000_000, $employee->fresh(['taxProfile', 'bpjsProfile']));

    $calc = new Pph21TerCalculator(new TerTable(), new PtkpResolver());
    $result = $calc->calculate($employee->fresh('taxProfile'), 8_000_000, $bpjs);

    $base   = round(8_363_200 * 0.015, 2);
    $expect = round($base * 1.20, 2);

    expect($result->pph21Amount)->toBe($expect)
        ->and($result->nonNpwpSurchargeApplied)->toBeTrue();
});
