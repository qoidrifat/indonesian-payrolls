<?php

namespace App\Services\Payroll;

/**
 * Simplified PPh 21 calculation using TER (Tarif Efektif Rata-rata)
 * Kategori A per PMK 168/2023 (applies to TK/0, TK/1, K/0).
 * Kategori B/C use slightly higher brackets; for the small-company use
 * case we approximate using category A & B brackets and a +5% penalty
 * when the employee has no NPWP.
 *
 * Brackets are simplified for readability — production deployments
 * should load the full table from a config file or DB.
 */
class Pph21Calculator
{
    /**
     * @var array<int,array{0:float,1:float}> [threshold, effective rate]
     */
    private array $terA = [
        [5_400_000,   0.0000],
        [5_650_000,   0.0025],
        [5_950_000,   0.0050],
        [6_300_000,   0.0075],
        [6_750_000,   0.0100],
        [7_500_000,   0.0125],
        [8_550_000,   0.0150],
        [9_650_000,   0.0175],
        [10_050_000,  0.0200],
        [10_350_000,  0.0225],
        [10_700_000,  0.0250],
        [11_050_000,  0.0300],
        [11_600_000,  0.0350],
        [12_500_000,  0.0400],
        [13_750_000,  0.0500],
        [15_100_000,  0.0600],
        [16_950_000,  0.0700],
        [19_750_000,  0.0800],
        [24_150_000,  0.0900],
        [26_450_000,  0.1000],
        [28_000_000,  0.1100],
        [30_050_000,  0.1200],
        [32_400_000,  0.1300],
        [35_400_000,  0.1400],
        [39_100_000,  0.1500],
        [43_850_000,  0.1600],
        [47_800_000,  0.1700],
        [51_400_000,  0.1800],
        [56_300_000,  0.1900],
        [62_200_000,  0.2000],
        [69_200_000,  0.2100],
        [77_500_000,  0.2200],
        [89_000_000,  0.2300],
        [103_000_000, 0.2400],
        [125_000_000, 0.2500],
        [157_000_000, 0.2600],
        [206_000_000, 0.2700],
        [337_000_000, 0.2800],
        [454_000_000, 0.2900],
        [550_000_000, 0.3000],
        [695_000_000, 0.3100],
        [910_000_000, 0.3200],
        [1_400_000_000, 0.3300],
        [PHP_INT_MAX, 0.3400],
    ];

    /**
     * Compute monthly PPh 21 withholding.
     */
    public function compute(float $taxableMonthly, string $ptkpStatus = 'TK', bool $hasNpwp = true): float
    {
        if ($taxableMonthly <= 0) {
            return 0.0;
        }

        $rate = $this->lookupRate($taxableMonthly);
        // PTKP K1/K2/K3 — apply a small reduction to the effective rate
        $rate = match ($ptkpStatus) {
            'K1' => max(0, $rate - 0.0025),
            'K2' => max(0, $rate - 0.0050),
            'K3' => max(0, $rate - 0.0075),
            default => $rate,
        };

        $tax = $taxableMonthly * $rate;

        if (! $hasNpwp) {
            $tax *= 1.20; // +20% penalty per Article 21(5a) UU PPh
        }

        return round($tax, 2);
    }

    private function lookupRate(float $income): float
    {
        foreach ($this->terA as [$threshold, $rate]) {
            if ($income <= $threshold) {
                return $rate;
            }
        }

        return 0.34;
    }
}
