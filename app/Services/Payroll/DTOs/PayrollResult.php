<?php

namespace App\Services\Payroll\DTOs;

class PayrollResult
{
    public function __construct(
        public float $baseSalary,
        public float $allowances,
        public float $overtimePay,
        public float $bonus,
        public float $grossSalary,
        public float $bpjsKesehatanEmployee,
        public float $bpjsKesehatanCompany,
        public float $bpjsJhtEmployee,
        public float $bpjsJhtCompany,
        public float $bpjsJpEmployee,
        public float $bpjsJpCompany,
        public float $pph21,
        public float $otherDeductions,
        public float $totalDeductions,
        public float $netSalary,
        public array $breakdown = [],
    ) {}

    public function toArray(): array
    {
        return [
            'base_salary' => round($this->baseSalary, 2),
            'allowances' => round($this->allowances, 2),
            'overtime_pay' => round($this->overtimePay, 2),
            'bonus' => round($this->bonus, 2),
            'gross_salary' => round($this->grossSalary, 2),
            'bpjs_kesehatan_employee' => round($this->bpjsKesehatanEmployee, 2),
            'bpjs_kesehatan_company' => round($this->bpjsKesehatanCompany, 2),
            'bpjs_jht_employee' => round($this->bpjsJhtEmployee, 2),
            'bpjs_jht_company' => round($this->bpjsJhtCompany, 2),
            'bpjs_jp_employee' => round($this->bpjsJpEmployee, 2),
            'bpjs_jp_company' => round($this->bpjsJpCompany, 2),
            'pph21' => round($this->pph21, 2),
            'other_deductions' => round($this->otherDeductions, 2),
            'total_deductions' => round($this->totalDeductions, 2),
            'net_salary' => round($this->netSalary, 2),
            'breakdown' => $this->breakdown,
        ];
    }
}
