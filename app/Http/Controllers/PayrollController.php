<?php

namespace App\Http\Controllers;

use App\Actions\Payroll\RunPayroll;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PayrollController extends Controller
{
    public function index(Request $request): Response
    {
        $payrolls = Payroll::orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->withCount('items')
            ->paginate(20);

        return Inertia::render('Payroll/Index', [
            'payrolls' => $payrolls,
        ]);
    }

    public function create(): Response
    {
        $now = Carbon::now();

        return Inertia::render('Payroll/Form', [
            'payroll' => null,
            'defaults' => [
                'period_year' => $now->year,
                'period_month' => $now->month,
                'period_start' => $now->copy()->startOfMonth()->toDateString(),
                'period_end' => $now->copy()->endOfMonth()->toDateString(),
                'pay_date' => $now->copy()->endOfMonth()->day(min(25, $now->copy()->endOfMonth()->day))->toDateString(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'period_year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'pay_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['code'] = sprintf('PR-%04d%02d', $data['period_year'], $data['period_month']);
        $data['status'] = 'draft';
        $data['created_by'] = $request->user()->id;

        $payroll = Payroll::create($data);

        return redirect()->route('payrolls.show', $payroll)
            ->with('success', 'Payroll created — review and run calculation');
    }

    public function show(Payroll $payroll): Response
    {
        $payroll->load(['items.employee', 'items.payslip', 'creator:id,name', 'approver:id,name']);

        return Inertia::render('Payroll/Show', [
            'payroll' => $payroll,
            'totals' => [
                'gross' => (float) $payroll->total_gross,
                'deductions' => (float) $payroll->total_deductions,
                'net' => (float) $payroll->total_net,
            ],
        ]);
    }

    public function run(Payroll $payroll, RunPayroll $action): RedirectResponse
    {
        abort_unless(in_array($payroll->status, ['draft', 'calculated']), 422, 'Payroll cannot be recalculated.');
        $action->execute($payroll);

        return back()->with('success', 'Payroll calculated');
    }

    public function approve(Request $request, Payroll $payroll): RedirectResponse
    {
        abort_unless($payroll->status === 'calculated', 422, 'Payroll must be calculated first.');
        $payroll->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Payroll approved');
    }

    public function pay(Request $request, Payroll $payroll): RedirectResponse
    {
        abort_unless($payroll->status === 'approved', 422, 'Payroll must be approved first.');
        $payroll->update([
            'status' => 'paid',
            'pay_date' => $request->input('pay_date', now()->toDateString()),
        ]);

        return back()->with('success', 'Payroll marked as paid');
    }

    public function destroy(Payroll $payroll): RedirectResponse
    {
        abort_if($payroll->status === 'paid', 422, 'Cannot delete a paid payroll.');
        $payroll->delete();

        return redirect()->route('payrolls.index')->with('success', 'Payroll deleted');
    }
}
