<?php

namespace App\Services\Payroll;

use App\DTO\OvertimeResultDTO;

/**
 * Calculates overtime pay per PP 35/2021 Pasal 31.
 *
 * Hourly rate divisor = 1/173 × monthly wage (gaji pokok + tunjangan tetap).
 *
 * Workdays:
 *   Hour 1     : 1.5×
 *   Hour 2+    : 2×
 *
 * Rest day / public holiday — 6-day workweek:
 *   Hour 1–7   : 2×
 *   Hour 8     : 3×
 *   Hour 9–10  : 4×
 *
 * Rest day / public holiday — 5-day workweek:
 *   Hour 1–8   : 2×
 *   Hour 9     : 3×
 *   Hour 10–11 : 4×
 */
class OvertimeCalculator
{
    public function calculate(
        float $monthlyWage,
        float $hours,
        string $dayType = 'workday',
        string $workweek = 'five_days',
    ): OvertimeResultDTO {
        $hourly = $monthlyWage / 173.0;

        $multipliers = $this->multipliersFor($dayType, $workweek);

        $totalHours = (int) floor($hours);
        $fractional = $hours - $totalHours;

        $amount    = 0.0;
        $breakdown = [];

        for ($h = 1; $h <= $totalHours; $h++) {
            $multiplier = $this->multiplierForHour($multipliers, $h);
            $hourAmount = round($hourly * $multiplier, 2);
            $amount    += $hourAmount;
            $breakdown[] = [
                'hour'       => $h,
                'multiplier' => $multiplier,
                'amount'     => $hourAmount,
            ];
        }

        if ($fractional > 0) {
            $multiplier = $this->multiplierForHour($multipliers, $totalHours + 1);
            $partial    = round($hourly * $multiplier * $fractional, 2);
            $amount    += $partial;
            $breakdown[] = [
                'hour'       => $totalHours + 1,
                'multiplier' => $multiplier,
                'amount'     => $partial,
                'fraction'   => $fractional,
            ];
        }

        return new OvertimeResultDTO(
            hours:      $hours,
            hourlyRate: round($hourly, 2),
            amount:     round($amount, 2),
            breakdown:  $breakdown,
        );
    }

    /** @return array<int, float> */
    private function multipliersFor(string $dayType, string $workweek): array
    {
        if ($dayType === 'workday') {
            // index = hour number; we use ::multiplierForHour for any beyond explicit list
            return [1 => 1.5, 'rest' => 2.0];
        }

        if ($workweek === 'six_days') {
            return [
                1 => 2.0, 2 => 2.0, 3 => 2.0, 4 => 2.0, 5 => 2.0, 6 => 2.0, 7 => 2.0,
                8 => 3.0,
                9 => 4.0, 10 => 4.0,
                'rest' => 4.0,
            ];
        }

        // five_days rest day / public holiday
        return [
            1 => 2.0, 2 => 2.0, 3 => 2.0, 4 => 2.0, 5 => 2.0, 6 => 2.0, 7 => 2.0, 8 => 2.0,
            9 => 3.0,
            10 => 4.0, 11 => 4.0,
            'rest' => 4.0,
        ];
    }

    /**
     * @param  array<int|string, float>  $multipliers
     */
    private function multiplierForHour(array $multipliers, int $hour): float
    {
        return $multipliers[$hour] ?? $multipliers['rest'];
    }
}
