<?php

namespace App\Services\Payroll;

use App\Services\Payroll\DTOs\PayrollInput;
use App\Services\Payroll\DTOs\PayrollResult;

class PayrollEngine
{
    public function __construct(
        public BpjsCalculator $bpjs = new BpjsCalculator,
        public Pph21Calculator $pph21 = new Pph21Calculator,
        public OvertimeCalculator $overtime = new OvertimeCalculator,
    ) {}

    public function calculate(PayrollInput $in): PayrollResult
    {
        // Pro-rate base salary by attendance days.
        $proratedBase = $in->workingDays > 0
            ? $in->baseSalary * ($in->presentDays / $in->workingDays)
            : $in->baseSalary;

        $allowances = $in->allowanceTotal();
        $taxableAllowances = $in->taxableAllowances();

        $gross = $proratedBase + $allowances + $in->overtimePay + $in->bonus;

        $bpjs = $this->bpjs->compute($proratedBase);
        $bpjsEmployeeTotal = $bpjs['kesehatan_employee']
            + $bpjs['jht_employee']
            + $bpjs['jp_employee'];

        // PPh 21 base: taxable income before employee BPJS deductions
        // (BPJS contributions are partially tax-deductible per UU PPh 21).
        $pph21Base = $proratedBase + $taxableAllowances + $in->overtimePay + $in->bonus;
        $pph21 = $this->pph21->compute($pph21Base, $in->ptkpStatus, $in->hasNpwp);

        $totalDeductions = $bpjsEmployeeTotal + $pph21 + $in->otherDeductions;
        $net = $gross - $totalDeductions;

        return new PayrollResult(
            baseSalary: round($proratedBase, 2),
            allowances: round($allowances, 2),
            overtimePay: round($in->overtimePay, 2),
            bonus: round($in->bonus, 2),
            grossSalary: round($gross, 2),
            bpjsKesehatanEmployee: $bpjs['kesehatan_employee'],
            bpjsKesehatanCompany: $bpjs['kesehatan_company'],
            bpjsJhtEmployee: $bpjs['jht_employee'],
            bpjsJhtCompany: $bpjs['jht_company'],
            bpjsJpEmployee: $bpjs['jp_employee'],
            bpjsJpCompany: $bpjs['jp_company'],
            pph21: $pph21,
            otherDeductions: round($in->otherDeductions, 2),
            totalDeductions: round($totalDeductions, 2),
            netSalary: round($net, 2),
            breakdown: [
                'allowances' => $in->allowances,
                'pph21_base' => round($pph21Base, 2),
                'working_days' => $in->workingDays,
                'present_days' => $in->presentDays,
                'prorate_factor' => $in->workingDays
                    ? round($in->presentDays / $in->workingDays, 4) : 1,
            ],
        );
    }
}
