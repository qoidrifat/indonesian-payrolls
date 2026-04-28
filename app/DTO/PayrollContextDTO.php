<?php

namespace App\DTO;

use App\Models\Employee;
use App\Models\PayrollPeriod;

final readonly class PayrollContextDTO
{
    public function __construct(
        public Employee $employee,
        public PayrollPeriod $period,
        public float $basicSalary,
        public float $fixedAllowance,
        public float $variableAllowance,
        public float $overtimeAmount,
        public float $bonusAmount,
        public float $thrAmount,
        public float $otherEarnings,
        public float $otherDeductions,
        public bool $isDecemberReconciliation = false,
    ) {
    }

    public function bpjsBaseWage(): float
    {
        return round($this->basicSalary + $this->fixedAllowance, 2);
    }

    public function grossSalary(): float
    {
        return round(
            $this->basicSalary + $this->fixedAllowance + $this->variableAllowance
            + $this->overtimeAmount + $this->bonusAmount + $this->thrAmount + $this->otherEarnings,
            2
        );
    }
}
