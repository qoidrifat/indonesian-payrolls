<?php

namespace App\Services\Tax;

class PtkpResolver
{
    public function amountFor(string $status): float
    {
        $values = config('payroll.ptkp.values');

        return (float) ($values[$status] ?? $values['TK/0']);
    }

    public function terCategoryFor(string $status): string
    {
        $map = config('payroll.ptkp.ter_category');

        return $map[$status] ?? 'A';
    }
}
