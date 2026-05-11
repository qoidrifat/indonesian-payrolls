<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceSummaryExport;
use App\Exports\MonthlyPayrollExport;
use App\Exports\TaxSummaryExport;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $payrolls = Payroll::orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->limit(24)
            ->get(['id', 'code', 'period_year', 'period_month', 'status', 'total_gross', 'total_net'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'code' => $p->code,
                'period' => $p->periodLabel(),
                'status' => $p->status,
                'total_gross' => (float) $p->total_gross,
                'total_net' => (float) $p->total_net,
            ]);

        $taxByMonth = PayrollItem::query()
            ->join('payrolls', 'payrolls.id', '=', 'payroll_items.payroll_id')
            ->selectRaw('payrolls.period_year as y, payrolls.period_month as m, SUM(pph21) as pph21')
            ->groupBy('y', 'm')
            ->orderBy('y')->orderBy('m')
            ->limit(12)
            ->get();

        $employees = Employee::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'employee_code', 'position']);

        return Inertia::render('Reports/Index', [
            'payrolls' => $payrolls,
            'tax_by_month' => $taxByMonth,
            'employees' => $employees,
        ]);
    }

    public function payrollExcel(Payroll $payroll)
    {
        $payroll->load('items.employee');

        return Excel::download(
            new MonthlyPayrollExport($payroll),
            sprintf('payroll-%s.xlsx', $payroll->code),
        );
    }

    public function payrollPdf(Payroll $payroll)
    {
        $payroll->load('items.employee');

        return Pdf::loadView('reports.payroll', ['payroll' => $payroll])
            ->setPaper('a4', 'landscape')
            ->stream(sprintf('payroll-%s.pdf', $payroll->code));
    }

    public function taxExcel(Request $request)
    {
        $year = (int) $request->query('year', now()->year);

        return Excel::download(new TaxSummaryExport($year), "tax-summary-$year.xlsx");
    }

    public function attendanceExcel(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));

        return Excel::download(new AttendanceSummaryExport($month), "attendance-$month.xlsx");
    }

    public function employeeHistory(Employee $employee)
    {
        $employee->load(['payrollItems.payroll']);

        return Pdf::loadView('reports.employee-history', ['employee' => $employee])
            ->setPaper('a4')
            ->stream(sprintf('salary-history-%s.pdf', $employee->employee_code));
    }
}
