<?php

namespace Tests\Feature;

use App\Actions\Payroll\RunPayroll;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_payroll_creates_one_item_per_active_employee(): void
    {
        $employees = Employee::factory()->count(3)->create(['is_active' => true]);

        $start = Carbon::create(2026, 4, 1);
        $end = $start->copy()->endOfMonth();
        $payroll = Payroll::create([
            'code' => 'PR-202604',
            'period_year' => 2026,
            'period_month' => 4,
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'status' => 'draft',
        ]);

        foreach ($employees as $e) {
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                if ($d->isWeekend()) {
                    continue;
                }
                Attendance::create([
                    'employee_id' => $e->id,
                    'date' => $d->toDateString(),
                    'status' => 'present',
                ]);
            }
        }

        (new RunPayroll)->execute($payroll);
        $payroll->refresh();

        $this->assertEquals(3, $payroll->items()->count());
        $this->assertEquals('calculated', $payroll->status);
        $this->assertGreaterThan(0, $payroll->total_net);
        $this->assertGreaterThan($payroll->total_net, $payroll->total_gross);
    }

    public function test_running_twice_replaces_items(): void
    {
        Employee::factory()->create(['is_active' => true]);
        $payroll = Payroll::create([
            'code' => 'PR-202605',
            'period_year' => 2026, 'period_month' => 5,
            'period_start' => '2026-05-01', 'period_end' => '2026-05-31',
            'status' => 'draft',
        ]);

        (new RunPayroll)->execute($payroll);
        $firstNet = $payroll->fresh()->total_net;

        (new RunPayroll)->execute($payroll);
        $this->assertEquals(1, PayrollItem::where('payroll_id', $payroll->id)->count());
    }
}
