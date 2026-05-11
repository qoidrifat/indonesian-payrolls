<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $now = CarbonImmutable::now();
        $monthStart = $now->startOfMonth();

        $employeesActive = Employee::where('is_active', true)->count();
        $employeesTotal = Employee::count();
        $employeesNew = Employee::where('join_date', '>=', $monthStart)->count();

        $payrollsPaid = Payroll::where('status', 'paid')->count();
        $payrollsDraft = Payroll::whereIn('status', ['draft', 'calculated'])->count();

        $latestPayroll = Payroll::orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->with('items')
            ->first();

        $monthlyTrend = PayrollItem::query()
            ->join('payrolls', 'payrolls.id', '=', 'payroll_items.payroll_id')
            ->selectRaw('payrolls.period_year as y, payrolls.period_month as m, SUM(payroll_items.net_salary) as net, SUM(payroll_items.gross_salary) as gross')
            ->where('payrolls.period_year', '>=', $now->year - 1)
            ->groupBy('y', 'm')
            ->orderBy('y')->orderBy('m')
            ->get();

        $departmentMix = Employee::query()
            ->selectRaw('coalesce(department, "Unassigned") as label, count(*) as value')
            ->where('is_active', true)
            ->groupBy('label')->get();

        $attendanceToday = [
            'present' => Attendance::whereDate('date', $now->toDateString())->where('status', 'present')->count(),
            'remote' => Attendance::whereDate('date', $now->toDateString())->where('status', 'remote')->count(),
            'leave' => Attendance::whereDate('date', $now->toDateString())->where('status', 'leave')->count(),
            'sick' => Attendance::whereDate('date', $now->toDateString())->where('status', 'sick')->count(),
            'absent' => Attendance::whereDate('date', $now->toDateString())->where('status', 'absent')->count(),
        ];

        $nextPayrollDate = $now->copy()->startOfMonth()->addMonth()->day(min(25, $now->copy()->addMonth()->daysInMonth));

        $activity = Activity::query()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'description' => $a->description,
                'subject' => $a->subject_type ? class_basename($a->subject_type) : null,
                'causer' => optional($a->causer)->name,
                'created_at' => $a->created_at?->diffForHumans(),
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'employees_active' => $employeesActive,
                'employees_total' => $employeesTotal,
                'employees_new' => $employeesNew,
                'payrolls_paid' => $payrollsPaid,
                'payrolls_draft' => $payrollsDraft,
                'this_month_net' => (float) ($latestPayroll?->total_net ?? 0),
                'this_month_gross' => (float) ($latestPayroll?->total_gross ?? 0),
            ],
            'monthly_trend' => $monthlyTrend,
            'department_mix' => $departmentMix,
            'attendance_today' => $attendanceToday,
            'latest_payroll' => $latestPayroll ? [
                'id' => $latestPayroll->id,
                'code' => $latestPayroll->code,
                'period' => $latestPayroll->periodLabel(),
                'status' => $latestPayroll->status,
                'total_net' => (float) $latestPayroll->total_net,
                'employee_count' => $latestPayroll->employee_count,
            ] : null,
            'next_payroll_date' => $nextPayrollDate->toDateString(),
            'activity' => $activity,
        ]);
    }
}
