<?php

namespace App\Actions\Payroll;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Services\Payroll\DTOs\PayrollInput;
use App\Services\Payroll\OvertimeCalculator;
use App\Services\Payroll\PayrollEngine;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class RunPayroll
{
    public function __construct(
        public PayrollEngine $engine = new PayrollEngine,
        public OvertimeCalculator $overtime = new OvertimeCalculator,
    ) {}

    public function execute(Payroll $payroll): Payroll
    {
        $start = CarbonImmutable::parse($payroll->period_start);
        $end = CarbonImmutable::parse($payroll->period_end);
        $workingDays = $this->workingDaysBetween($start, $end);

        $employees = Employee::query()
            ->with('salaryComponents')
            ->where('is_active', true)
            ->whereDate('join_date', '<=', $end)
            ->where(function ($q) use ($start) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $start);
            })
            ->get();

        DB::transaction(function () use ($payroll, $employees, $start, $end, $workingDays) {
            $payroll->items()->delete();

            $totals = ['gross' => 0.0, 'ded' => 0.0, 'net' => 0.0, 'count' => 0];

            foreach ($employees as $employee) {
                $presentDays = $this->countPresentDays($employee->id, $start, $end);
                $overtimeMinutes = $this->countOvertimeMinutes($employee->id, $start, $end);
                $overtimePay = $this->overtime->fromMinutes(
                    (float) $employee->base_salary,
                    $overtimeMinutes,
                );

                $allowances = [];
                $bonus = 0.0;
                $otherDed = 0.0;
                foreach ($employee->salaryComponents as $c) {
                    if (! $c->is_recurring) {
                        continue;
                    }
                    if ($c->type === 'allowance') {
                        $allowances[] = [
                            'name' => $c->name,
                            'amount' => (float) $c->amount,
                            'taxable' => (bool) $c->is_taxable,
                        ];
                    } elseif ($c->type === 'bonus') {
                        $bonus += (float) $c->amount;
                    } elseif ($c->type === 'deduction') {
                        $otherDed += (float) $c->amount;
                    }
                }

                $input = new PayrollInput(
                    baseSalary: (float) $employee->base_salary,
                    ptkpStatus: $employee->marital_status ?? 'TK',
                    hasNpwp: ! empty($employee->npwp),
                    overtimePay: $overtimePay,
                    bonus: $bonus,
                    otherDeductions: $otherDed,
                    workingDays: $workingDays,
                    presentDays: $presentDays,
                    allowances: $allowances,
                );

                $result = $this->engine->calculate($input);

                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $employee->id,
                    ...$result->toArray(),
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'overtime_minutes' => $overtimeMinutes,
                ]);

                $totals['gross'] += $result->grossSalary;
                $totals['ded'] += $result->totalDeductions;
                $totals['net'] += $result->netSalary;
                $totals['count']++;
            }

            $payroll->update([
                'status' => 'calculated',
                'total_gross' => round($totals['gross'], 2),
                'total_deductions' => round($totals['ded'], 2),
                'total_net' => round($totals['net'], 2),
                'employee_count' => $totals['count'],
            ]);
        });

        return $payroll->fresh('items.employee');
    }

    public function workingDaysBetween(CarbonImmutable $start, CarbonImmutable $end): int
    {
        $count = 0;
        foreach (CarbonPeriod::create($start, $end) as $day) {
            if (! $day->isWeekend()) {
                $count++;
            }
        }

        return max(1, $count);
    }

    public function countPresentDays(int $employeeId, CarbonImmutable $start, CarbonImmutable $end): int
    {
        $logged = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['present', 'remote'])
            ->count();

        return $logged > 0 ? $logged : $this->workingDaysBetween($start, $end);
    }

    public function countOvertimeMinutes(int $employeeId, CarbonImmutable $start, CarbonImmutable $end): int
    {
        return (int) Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('overtime_minutes');
    }
}
