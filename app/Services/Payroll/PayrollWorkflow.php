<?php

namespace App\Services\Payroll;

use App\DTO\PayrollContextDTO;
use App\Models\Allowance;
use App\Models\AttendanceLog;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\TaxCalculation;
use App\Models\BpjsCalculation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * State machine for a payroll run:
 *   draft → calculating → calculated → approved → paid
 *
 * Persists per-employee PayrollItem + TaxCalculation + BpjsCalculation rows
 * using the pure-domain PayrollEngine.
 */
class PayrollWorkflow
{
    public function __construct(
        private readonly PayrollEngine $engine,
        private readonly OvertimeCalculator $overtime,
    ) {
    }

    public function calculate(PayrollRun $run): PayrollRun
    {
        $period = $run->period()->firstOrFail();

        DB::transaction(function () use ($run, $period) {
            $run->update(['status' => 'calculating']);

            $totals = [
                'gross'          => 0.0,
                'net'            => 0.0,
                'tax'            => 0.0,
                'bpjs_employee'  => 0.0,
                'bpjs_employer'  => 0.0,
                'count'          => 0,
            ];

            Employee::query()
                ->where('status', 'active')
                ->where(function ($q) use ($period) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $period->start_date);
                })
                ->where('hire_date', '<=', $period->end_date)
                ->with(['taxProfile', 'bpjsProfile'])
                ->chunkById(100, function ($employees) use ($run, $period, &$totals) {
                    foreach ($employees as $employee) {
                        $result = $this->calculateForEmployee($employee, $period, $run);
                        if ($result === null) {
                            continue;
                        }

                        $totals['gross']         += (float) $result->grossSalary;
                        $totals['net']           += (float) $result->netSalary;
                        $totals['tax']           += (float) $result->tax->pph21Amount;
                        $totals['bpjs_employee'] += (float) $result->bpjs->totalEmployee();
                        $totals['bpjs_employer'] += (float) $result->bpjs->totalEmployer();
                        $totals['count']++;
                    }
                });

            $run->update([
                'status'              => 'calculated',
                'calculated_at'       => now(),
                'employee_count'      => $totals['count'],
                'total_gross'         => round($totals['gross'], 2),
                'total_net'           => round($totals['net'], 2),
                'total_tax'           => round($totals['tax'], 2),
                'total_bpjs_employee' => round($totals['bpjs_employee'], 2),
                'total_bpjs_employer' => round($totals['bpjs_employer'], 2),
            ]);
        });

        return $run->refresh();
    }

    public function approve(PayrollRun $run, ?int $userId = null): PayrollRun
    {
        if ($run->status !== 'calculated') {
            throw new \DomainException("Cannot approve payroll run in status [{$run->status}].");
        }

        $run->update([
            'status'      => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        return $run->refresh();
    }

    public function markPaid(PayrollRun $run): PayrollRun
    {
        if ($run->status !== 'approved') {
            throw new \DomainException("Cannot mark paid for payroll run in status [{$run->status}].");
        }

        $run->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        return $run->refresh();
    }

    public function calculateForEmployee(Employee $employee, PayrollPeriod $period, PayrollRun $run): ?\App\DTO\PayrollResultDTO
    {
        $salary = $employee->activeSalary(Carbon::parse($period->end_date));

        if ($salary === null) {
            return null;
        }

        $overtimeAmount = $this->sumOvertimeForPeriod($employee, $period, (float) $salary->bpjsBaseWage());
        $bonusAmount    = $this->sumAllowances($employee, $period, ['bonus', 'incentive']);
        $thrAmount      = $period->is_thr_period ? $this->sumAllowances($employee, $period, ['thr']) : 0.0;
        $otherEarnings  = $this->sumAllowances($employee, $period, ['reimbursement', 'other']);
        $otherDeducts   = $this->sumDeductions($employee, $period);

        $ctx = new PayrollContextDTO(
            employee:                 $employee,
            period:                   $period,
            basicSalary:              (float) $salary->basic_salary,
            fixedAllowance:           (float) $salary->fixed_allowance_total,
            variableAllowance:        (float) $salary->variable_allowance_total,
            overtimeAmount:           $overtimeAmount,
            bonusAmount:              $bonusAmount,
            thrAmount:                $thrAmount,
            otherEarnings:            $otherEarnings,
            otherDeductions:          $otherDeducts,
            isDecemberReconciliation: ((int) $period->month === 12),
        );

        // YTD totals (used for December reconciliation only, but cheap to compute).
        $ytd = $this->ytdTotals($employee, $period);

        $result = $this->engine->calculate(
            $ctx,
            ytdGross:          $ytd['gross'],
            ytdJhtJpEmployee:  $ytd['jht_jp_employee'],
            ytdPph21:          $ytd['pph21'],
        );

        $idempotency = hash('sha256', "{$run->id}:{$employee->id}:{$period->id}");

        $item = PayrollItem::updateOrCreate(
            ['idempotency_key' => $idempotency],
            [
                'payroll_run_id'      => $run->id,
                'employee_id'         => $employee->id,
                'basic_salary'        => $ctx->basicSalary,
                'fixed_allowance'     => $ctx->fixedAllowance,
                'variable_allowance'  => $ctx->variableAllowance,
                'overtime_amount'     => $ctx->overtimeAmount,
                'thr_amount'          => $ctx->thrAmount,
                'bonus_amount'        => $ctx->bonusAmount,
                'other_earnings'      => $ctx->otherEarnings,
                'gross_salary'        => $result->grossSalary,
                'bpjs_employee_total' => $result->bpjs->totalEmployee(),
                'bpjs_employer_total' => $result->bpjs->totalEmployer(),
                'pph21_amount'        => $result->tax->pph21Amount,
                'other_deductions'    => $ctx->otherDeductions,
                'total_deductions'    => $result->totalDeductions,
                'net_salary'          => $result->netSalary,
                'breakdown'           => $result->toArray(),
            ]
        );

        TaxCalculation::updateOrCreate(
            ['payroll_item_id' => $item->id],
            $result->tax->toArray()
        );

        BpjsCalculation::updateOrCreate(
            ['payroll_item_id' => $item->id],
            $result->bpjs->toArray()
        );

        return $result;
    }

    private function sumOvertimeForPeriod(Employee $employee, PayrollPeriod $period, float $monthlyWage): float
    {
        $rows = Overtime::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('work_date', [$period->start_date, $period->end_date])
            ->where('status', 'approved')
            ->get();

        $total = 0.0;
        foreach ($rows as $row) {
            $result  = $this->overtime->calculate(
                monthlyWage: $monthlyWage,
                hours:       (float) $row->hours,
                dayType:     $row->day_type,
                workweek:    $employee->workweek ?? 'five_days',
            );
            $total += $result->amount;
        }

        return round($total, 2);
    }

    private function sumAllowances(Employee $employee, PayrollPeriod $period, array $types): float
    {
        return (float) Allowance::query()
            ->where('employee_id', $employee->id)
            ->where('payroll_period_id', $period->id)
            ->whereIn('type', $types)
            ->sum('amount');
    }

    private function sumDeductions(Employee $employee, PayrollPeriod $period): float
    {
        return (float) Deduction::query()
            ->where('employee_id', $employee->id)
            ->where('payroll_period_id', $period->id)
            ->sum('amount');
    }

    /**
     * @return array{gross: float, jht_jp_employee: float, pph21: float}
     */
    private function ytdTotals(Employee $employee, PayrollPeriod $period): array
    {
        $items = PayrollItem::query()
            ->where('employee_id', $employee->id)
            ->whereHas('run.period', function ($q) use ($period) {
                $q->where('year', $period->year)
                    ->where('month', '<', $period->month);
            })
            ->with('bpjsCalculation')
            ->get();

        $gross = 0.0;
        $jhtJp = 0.0;
        $pph21 = 0.0;

        foreach ($items as $item) {
            $gross += (float) $item->gross_salary;
            $pph21 += (float) $item->pph21_amount;
            if ($item->bpjsCalculation) {
                $jhtJp += (float) $item->bpjsCalculation->jht_employee
                       + (float) $item->bpjsCalculation->jp_employee;
            }
        }

        return [
            'gross'           => $gross,
            'jht_jp_employee' => $jhtJp,
            'pph21'           => $pph21,
        ];
    }
}
