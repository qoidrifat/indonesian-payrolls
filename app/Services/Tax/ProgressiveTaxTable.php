<?php

namespace App\Services\Tax;

class ProgressiveTaxTable
{
    /**
     * Apply progressive PPh 21 brackets (UU HPP / UU 7/2021) to taxable income (PKP).
     */
    public function calculate(float $pkp): float
    {
        if ($pkp <= 0) {
            return 0.0;
        }

        $brackets  = config('payroll.progressive');
        $remaining = $pkp;
        $previous  = 0.0;
        $tax       = 0.0;

        foreach ($brackets as $bracket) {
            $upTo = $bracket['up_to'];
            $rate = (float) $bracket['rate'];

            $sliceEnd   = $upTo === null ? $pkp : min($pkp, (float) $upTo);
            $sliceWidth = $sliceEnd - $previous;

            if ($sliceWidth > 0) {
                $tax       += $sliceWidth * $rate;
                $remaining -= $sliceWidth;
            }

            $previous = $sliceEnd;

            if ($upTo !== null && $pkp <= (float) $upTo) {
                break;
            }
        }

        return round($tax, 2);
    }
}
