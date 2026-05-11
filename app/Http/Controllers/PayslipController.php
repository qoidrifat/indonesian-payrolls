<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Services\Payslip\PayslipService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PayslipController extends Controller
{
    public function index(): Response
    {
        $payrolls = Payroll::where('status', '!=', 'draft')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->withCount('items')
            ->limit(24)
            ->get();

        return Inertia::render('Payslips/Index', [
            'payrolls' => $payrolls->map(fn ($p) => [
                'id' => $p->id,
                'code' => $p->code,
                'period' => $p->periodLabel(),
                'status' => $p->status,
                'employee_count' => $p->items_count,
                'total_net' => (float) $p->total_net,
            ]),
        ]);
    }

    public function show(PayrollItem $item, PayslipService $service)
    {
        $item->loadMissing('employee', 'payroll');

        return $service->stream($item)->stream(
            sprintf('payslip-%s-%s.pdf', $item->employee->employee_code, $item->payroll->code)
        );
    }

    public function generate(Payroll $payroll, PayslipService $service): RedirectResponse
    {
        abort_unless(in_array($payroll->status, ['calculated', 'approved', 'paid']), 422);
        $payroll->loadMissing('items.employee');
        foreach ($payroll->items as $item) {
            $service->generate($item);
        }

        return back()->with('success', 'Payslips generated for '.$payroll->items->count().' employees');
    }
}
