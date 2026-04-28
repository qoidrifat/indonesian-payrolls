<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\PayrollRun;
use App\Services\Payroll\PayrollWorkflow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Per-employee payroll calculation. Used to fan-out heavy runs.
 */
class CalculatePayrollItemJob implements ShouldQueue
{
    use FoundationQueueable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public int $payrollRunId,
        public int $employeeId,
    ) {
    }

    public function handle(PayrollWorkflow $workflow): void
    {
        $run = PayrollRun::with('period')->findOrFail($this->payrollRunId);
        $employee = Employee::with(['taxProfile', 'bpjsProfile'])->findOrFail($this->employeeId);

        $workflow->calculateForEmployee($employee, $run->period, $run);
    }

    public function uniqueId(): string
    {
        return "payroll:{$this->payrollRunId}:{$this->employeeId}";
    }
}
