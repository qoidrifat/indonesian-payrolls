<?php

namespace App\Services\Payroll;

/**
 * Overtime calculation per PP 35/2021, simplified for a small-team
 * 5-day workweek. Hourly wage = monthly / 173.
 */
class OvertimeCalculator
{
    public function __construct(public int $monthlyHourDivisor = 173) {}

    public function hourlyRate(float $monthlySalary): float
    {
        return $monthlySalary / $this->monthlyHourDivisor;
    }

    /**
     * Compute overtime pay on a workday.
     * - First hour: 1.5x hourly rate
     * - Subsequent hours: 2.0x hourly rate
     */
    public function workday(float $monthlySalary, float $hours): float
    {
        if ($hours <= 0) {
            return 0.0;
        }
        $rate = $this->hourlyRate($monthlySalary);
        $first = min($hours, 1) * 1.5 * $rate;
        $rest = max(0, $hours - 1) * 2.0 * $rate;

        return round($first + $rest, 2);
    }

    public function fromMinutes(float $monthlySalary, int $minutes): float
    {
        return $this->workday($monthlySalary, $minutes / 60);
    }
}
