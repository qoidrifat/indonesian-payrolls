<?php

namespace App\Services\Payroll\DTOs;

class PayrollInput
{
    /**
     * @param  array<int, array{name:string, amount:float, taxable?:bool}>  $allowances
     */
    public function __construct(
        public float $baseSalary,
        public string $ptkpStatus = 'TK',
        public bool $hasNpwp = true,
        public float $overtimePay = 0.0,
        public float $bonus = 0.0,
        public float $otherDeductions = 0.0,
        public int $workingDays = 22,
        public int $presentDays = 22,
        public array $allowances = [],
    ) {}

    public function allowanceTotal(): float
    {
        return array_sum(array_map(fn ($a) => (float) $a['amount'], $this->allowances));
    }

    public function taxableAllowances(): float
    {
        return array_sum(array_map(
            fn ($a) => ($a['taxable'] ?? true) ? (float) $a['amount'] : 0.0,
            $this->allowances,
        ));
    }
}
