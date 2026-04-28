<?php

namespace App\DTO;

final readonly class PayrollResultDTO
{
    public function __construct(
        public PayrollContextDTO $context,
        public BpjsResultDTO $bpjs,
        public TaxResultDTO $tax,
        public float $grossSalary,
        public float $totalDeductions,
        public float $netSalary,
    ) {
    }

    public function toArray(): array
    {
        return [
            'employee_id'       => $this->context->employee->id,
            'period'            => $this->context->period->code,
            'basic_salary'      => $this->context->basicSalary,
            'fixed_allowance'   => $this->context->fixedAllowance,
            'variable_allowance'=> $this->context->variableAllowance,
            'overtime_amount'   => $this->context->overtimeAmount,
            'thr_amount'        => $this->context->thrAmount,
            'bonus_amount'      => $this->context->bonusAmount,
            'other_earnings'    => $this->context->otherEarnings,
            'other_deductions'  => $this->context->otherDeductions,
            'gross_salary'      => $this->grossSalary,
            'total_deductions'  => $this->totalDeductions,
            'net_salary'        => $this->netSalary,
            'bpjs'              => $this->bpjs->toArray(),
            'tax'               => $this->tax->toArray(),
        ];
    }
}
