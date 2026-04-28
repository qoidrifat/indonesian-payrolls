<?php

namespace App\Services\Payroll;

use App\DTO\BpjsResultDTO;
use App\Models\Employee;

/**
 * Calculates BPJS Ketenagakerjaan (JHT, JP, JKK, JKM) and BPJS Kesehatan
 * contributions per PP 44/2015, PP 45/2015, PP 46/2015, Perpres 64/2020.
 *
 * Salary cap rules:
 *   JP        — capped at config('payroll.bpjs.jp.cap')        (Rp 10,547,400 in 2024)
 *   Kesehatan — capped at config('payroll.bpjs.kesehatan.cap') (Rp 12,000,000 in 2024)
 *   JHT/JKK/JKM — uncapped, but JHT/JKK/JKM use total wage
 *
 * BPJS Ketenagakerjaan base = `gaji pokok + tunjangan tetap`
 * BPJS Kesehatan base       = same (PMK 30/2019 / Perpres 64/2020)
 *
 * Risk grade for JKK is read from the employee's BpjsProfile (defaults to grade I).
 */
class BpjsCalculator
{
    /**
     * @param  float  $baseWage  gaji pokok + tunjangan tetap (uncapped input)
     */
    public function calculate(float $baseWage, ?Employee $employee = null): BpjsResultDTO
    {
        $config = config('payroll.bpjs');

        $profile = $employee?->bpjsProfile;

        $enroll = [
            'jht'       => $profile?->enroll_jht       ?? true,
            'jp'        => $profile?->enroll_jp        ?? true,
            'jkk'       => $profile?->enroll_jkk       ?? true,
            'jkm'       => $profile?->enroll_jkm       ?? true,
            'kesehatan' => $profile?->enroll_kesehatan ?? true,
        ];

        $jkkRate = $config['jkk']['risk_grades'][$profile?->jkk_risk_grade ?? 'I']
            ?? $config['jkk']['employer_rate'];

        $jpBase        = $this->capped($baseWage, $config['jp']['cap']);
        $kesehatanBase = $this->capped($baseWage, $config['kesehatan']['cap']);

        $jhtEmployee       = $enroll['jht']       ? $this->round($baseWage * $config['jht']['employee_rate'])      : 0.0;
        $jhtEmployer       = $enroll['jht']       ? $this->round($baseWage * $config['jht']['employer_rate'])      : 0.0;
        $jpEmployee        = $enroll['jp']        ? $this->round($jpBase   * $config['jp']['employee_rate'])       : 0.0;
        $jpEmployer        = $enroll['jp']        ? $this->round($jpBase   * $config['jp']['employer_rate'])       : 0.0;
        $jkkEmployer       = $enroll['jkk']       ? $this->round($baseWage * $jkkRate)                              : 0.0;
        $jkmEmployer       = $enroll['jkm']       ? $this->round($baseWage * $config['jkm']['employer_rate'])      : 0.0;
        $kesehatanEmployee = $enroll['kesehatan'] ? $this->round($kesehatanBase * $config['kesehatan']['employee_rate']) : 0.0;
        $kesehatanEmployer = $enroll['kesehatan'] ? $this->round($kesehatanBase * $config['kesehatan']['employer_rate']) : 0.0;

        return new BpjsResultDTO(
            baseWage:          $baseWage,
            jhtEmployee:       $jhtEmployee,
            jhtEmployer:       $jhtEmployer,
            jpEmployee:        $jpEmployee,
            jpEmployer:        $jpEmployer,
            jkkEmployer:       $jkkEmployer,
            jkmEmployer:       $jkmEmployer,
            kesehatanEmployee: $kesehatanEmployee,
            kesehatanEmployer: $kesehatanEmployer,
            ratesSnapshot:     [
                'jht'       => $config['jht'],
                'jp'        => $config['jp']        + ['applied_base' => $jpBase],
                'jkk'       => ['rate' => $jkkRate, 'risk_grade' => $profile?->jkk_risk_grade ?? 'I'],
                'jkm'       => $config['jkm'],
                'kesehatan' => $config['kesehatan'] + ['applied_base' => $kesehatanBase],
                'enroll'    => $enroll,
            ],
        );
    }

    private function capped(float $base, ?float $cap): float
    {
        if ($cap === null) {
            return $base;
        }

        return min($base, $cap);
    }

    private function round(float $v): float
    {
        return round($v, 2);
    }
}
