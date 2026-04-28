<?php

use App\Services\Payroll\BpjsCalculator;

it('computes BPJS for a wage below all caps', function () {
    $calc = new BpjsCalculator();
    $result = $calc->calculate(8_000_000, employee: null);

    expect($result->jhtEmployee)->toBe(160_000.0)        // 2.0%
        ->and($result->jhtEmployer)->toBe(296_000.0)     // 3.7%
        ->and($result->jpEmployee)->toBe(80_000.0)       // 1.0%
        ->and($result->jpEmployer)->toBe(160_000.0)      // 2.0%
        ->and($result->jkkEmployer)->toBe(19_200.0)      // 0.24% grade I
        ->and($result->jkmEmployer)->toBe(24_000.0)      // 0.30%
        ->and($result->kesehatanEmployee)->toBe(80_000.0) // 1.0%
        ->and($result->kesehatanEmployer)->toBe(320_000.0); // 4.0%

    expect($result->totalEmployee())->toBe(320_000.0);
});

it('caps JP base at 10,547,400', function () {
    $calc = new BpjsCalculator();
    // base above JP cap but below kesehatan cap
    $result = $calc->calculate(11_000_000, employee: null);

    expect($result->jpEmployee)->toBe(105_474.0)  // 1% × 10,547,400
        ->and($result->jpEmployer)->toBe(210_948.0); // 2% × 10,547,400
});

it('caps kesehatan base at 12,000,000', function () {
    $calc = new BpjsCalculator();
    $result = $calc->calculate(20_000_000, employee: null);

    expect($result->kesehatanEmployee)->toBe(120_000.0)   // 1% × 12,000,000
        ->and($result->kesehatanEmployer)->toBe(480_000.0); // 4% × 12,000,000

    // JHT and JKM remain on full base (uncapped)
    expect($result->jhtEmployee)->toBe(400_000.0); // 2% × 20,000,000
});
