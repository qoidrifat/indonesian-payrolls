<?php

namespace App\Services\Tax;

class TerTable
{
    /**
     * Return the TER rate for a given gross monthly income and category.
     */
    public function rateFor(string $category, float $grossMonthly): float
    {
        $brackets = config("payroll.ter.{$category}");

        if (! is_array($brackets)) {
            throw new \InvalidArgumentException("Unknown TER category: {$category}");
        }

        foreach ($brackets as $bracket) {
            $min = (float) $bracket['min'];
            $max = $bracket['max'];

            if ($grossMonthly >= $min && ($max === null || $grossMonthly < (float) $max)) {
                return (float) $bracket['rate'];
            }
        }

        // Fallback to top bracket (open-ended).
        $top = end($brackets);

        return (float) $top['rate'];
    }
}
