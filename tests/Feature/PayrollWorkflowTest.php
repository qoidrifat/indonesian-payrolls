<?php

use App\Models\BpjsProfile;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\TaxProfile;
use App\Services\Payroll\PayrollWorkflow;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('runs the full calculate → approve → mark paid lifecycle', function () {
    config(['payroll.bpjs.add_employer_to_taxable_income' => false]);

    $period = PayrollPeriod::create([
        'code'       => '2024-06',
        'year'       => 2024,
        'month'      => 6,
        'start_date' => '2024-06-01',
        'end_date'   => '2024-06-30',
        'status'     => 'open',
    ]);

    $employee = Employee::factory()->create([
        'hire_date' => '2024-01-01',
        'status'    => 'active',
    ]);
    TaxProfile::factory()->for($employee)->create([
        'has_npwp'     => true,
        'ptkp_status'  => 'TK/0',
        'ter_category' => 'A',
    ]);
    BpjsProfile::factory()->for($employee)->create();

    EmployeeSalary::create([
        'employee_id'              => $employee->id,
        'basic_salary'             => 8_000_000,
        'fixed_allowance_total'    => 1_000_000,
        'variable_allowance_total' => 0,
        'effective_from'           => '2024-01-01',
        'currency'                 => 'IDR',
    ]);

    $run = PayrollRun::create([
        'payroll_period_id' => $period->id,
        'name'              => 'June 2024',
        'run_type'          => 'monthly',
        'status'            => 'draft',
    ]);

    /** @var PayrollWorkflow $workflow */
    $workflow = app(PayrollWorkflow::class);

    $workflow->calculate($run);
    $run->refresh();

    expect($run->status)->toBe('calculated')
        ->and($run->employee_count)->toBe(1)
        ->and((float) $run->total_gross)->toBe(9_000_000.0); // basic + fixed allowance

    $item = $run->items()->firstOrFail();
    // BPJS base = 9,000,000 → JHT 180k + JP 90k + Kes 90k = 360k employee
    expect((float) $item->bpjs_employee_total)->toBe(360_000.0);

    // TER A on 9,000,000 (no natura because we disabled add_employer_to_taxable_income)
    // 9M falls in bracket 8,550,000–9,650,000 → 1.75%
    expect((float) $item->pph21_amount)->toBe(round(9_000_000 * 0.0175, 2));

    // net = 9M - 360k - 157,500 = 8,482,500
    $expectedNet = 9_000_000 - 360_000 - round(9_000_000 * 0.0175, 2);
    expect((float) $item->net_salary)->toBe(round($expectedNet, 2));

    $workflow->approve($run);
    expect($run->fresh()->status)->toBe('approved');

    $workflow->markPaid($run);
    expect($run->fresh()->status)->toBe('paid');
});
