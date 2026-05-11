<?php

namespace Tests\Unit;

use App\Services\Payroll\BpjsCalculator;
use App\Services\Payroll\DTOs\PayrollInput;
use App\Services\Payroll\OvertimeCalculator;
use App\Services\Payroll\PayrollEngine;
use App\Services\Payroll\Pph21Calculator;
use PHPUnit\Framework\TestCase;

class PayrollEngineTest extends TestCase
{
    private function engine(): PayrollEngine
    {
        return new PayrollEngine(new BpjsCalculator, new Pph21Calculator, new OvertimeCalculator);
    }

    public function test_calculates_simple_payroll_without_allowances(): void
    {
        $in = new PayrollInput(baseSalary: 10_000_000, ptkpStatus: 'TK', hasNpwp: true);
        $r = $this->engine()->calculate($in);

        $this->assertEquals(10_000_000, $r->baseSalary);
        $this->assertEquals(10_000_000, $r->grossSalary);
        $this->assertEquals(100_000, $r->bpjsKesehatanEmployee);
        $this->assertEquals(200_000, $r->bpjsJhtEmployee);
        $this->assertEquals(100_000, $r->bpjsJpEmployee);
        $this->assertGreaterThan(0, $r->pph21);
        $this->assertGreaterThan(0, $r->netSalary);
    }

    public function test_prorates_base_salary_by_attendance(): void
    {
        $in = new PayrollInput(
            baseSalary: 8_800_000,
            workingDays: 22,
            presentDays: 11,
        );
        $r = $this->engine()->calculate($in);
        $this->assertEquals(4_400_000, $r->baseSalary);
    }

    public function test_caps_bpjs_kesehatan_at_12m(): void
    {
        $calc = new BpjsCalculator;
        $high = $calc->compute(20_000_000);
        $this->assertEquals(120_000, $high['kesehatan_employee'], 'Kesehatan must be capped at 12M base');
        $this->assertEquals(480_000, $high['kesehatan_company']);
    }

    public function test_no_npwp_adds_20_percent(): void
    {
        $with = $this->engine()->calculate(new PayrollInput(baseSalary: 10_000_000, hasNpwp: true));
        $without = $this->engine()->calculate(new PayrollInput(baseSalary: 10_000_000, hasNpwp: false));
        $this->assertGreaterThan($with->pph21, $without->pph21);
        $this->assertEqualsWithDelta($with->pph21 * 1.20, $without->pph21, 1);
    }

    public function test_taxable_allowances_increase_pph21(): void
    {
        $base = new PayrollInput(baseSalary: 8_000_000);
        $withAllowance = new PayrollInput(
            baseSalary: 8_000_000,
            allowances: [['name' => 'transport', 'amount' => 1_500_000, 'taxable' => true]],
        );

        $a = $this->engine()->calculate($base);
        $b = $this->engine()->calculate($withAllowance);

        $this->assertGreaterThan($a->pph21, $b->pph21);
        $this->assertEquals(1_500_000, $b->allowances);
        $this->assertEquals(9_500_000, $b->grossSalary);
    }

    public function test_overtime_pay_calculator(): void
    {
        $ot = new OvertimeCalculator;
        // 60 min @ 8_800_000 monthly: hourly = 8_800_000 / 173 ≈ 50867
        // First hour rate 1.5x → ~76300
        $pay = $ot->fromMinutes(8_800_000, 60);
        $this->assertGreaterThan(70_000, $pay);
        $this->assertLessThan(85_000, $pay);
    }

    public function test_net_salary_equals_gross_minus_total_deductions(): void
    {
        $r = $this->engine()->calculate(new PayrollInput(
            baseSalary: 12_000_000,
            ptkpStatus: 'K1',
            bonus: 500_000,
            allowances: [['name' => 'transport', 'amount' => 750_000, 'taxable' => true]],
        ));
        $this->assertEqualsWithDelta($r->grossSalary - $r->totalDeductions, $r->netSalary, 0.01);
    }

    public function test_zero_salary_returns_zero_tax(): void
    {
        $r = $this->engine()->calculate(new PayrollInput(baseSalary: 0));
        $this->assertEquals(0, $r->pph21);
        $this->assertEquals(0, $r->grossSalary);
        $this->assertEquals(0, $r->netSalary);
    }
}
