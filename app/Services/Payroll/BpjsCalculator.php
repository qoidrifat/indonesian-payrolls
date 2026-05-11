<?php

namespace App\Services\Payroll;

/**
 * BPJS calculator per Perpres 64/2020 (Kesehatan) and
 * PP 44/2015 (JHT) + PP 45/2015 (JP). Rates are configurable but
 * default to the most commonly applied values for small Indonesian
 * IT/web companies.
 */
class BpjsCalculator
{
    public function __construct(
        public float $kesehatanCap = 12_000_000,
        public float $kesehatanEmployeeRate = 0.01,
        public float $kesehatanCompanyRate = 0.04,
        public float $jhtEmployeeRate = 0.02,
        public float $jhtCompanyRate = 0.037,
        public float $jpCap = 10_046_300,
        public float $jpEmployeeRate = 0.01,
        public float $jpCompanyRate = 0.02,
    ) {}

    /** @return array<string,float> */
    public function compute(float $base): array
    {
        $kesehatanBase = min($base, $this->kesehatanCap);
        $jpBase = min($base, $this->jpCap);

        return [
            'kesehatan_employee' => round($kesehatanBase * $this->kesehatanEmployeeRate, 2),
            'kesehatan_company' => round($kesehatanBase * $this->kesehatanCompanyRate, 2),
            'jht_employee' => round($base * $this->jhtEmployeeRate, 2),
            'jht_company' => round($base * $this->jhtCompanyRate, 2),
            'jp_employee' => round($jpBase * $this->jpEmployeeRate, 2),
            'jp_company' => round($jpBase * $this->jpCompanyRate, 2),
        ];
    }
}
