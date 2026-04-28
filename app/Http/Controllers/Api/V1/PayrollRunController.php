<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PayrollRun;
use App\Services\Payroll\PayrollWorkflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollRunController extends Controller
{
    public function __construct(private readonly PayrollWorkflow $workflow)
    {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            PayrollRun::query()->with('period')->latest('id')
                ->paginate($request->integer('per_page', 25))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'payroll_period_id' => ['required', 'exists:payroll_periods,id'],
            'name'              => ['required', 'string', 'max:120'],
            'run_type'          => ['required', 'in:monthly,thr,bonus,final_settlement'],
        ]);

        $run = PayrollRun::create($data + [
            'status'     => 'draft',
            'created_by' => $request->user()?->id,
        ]);

        return response()->json($run->load('period'), 201);
    }

    public function show(PayrollRun $run): JsonResponse
    {
        return response()->json($run->load(['period', 'items.employee']));
    }

    public function calculate(PayrollRun $run): JsonResponse
    {
        $this->workflow->calculate($run);
        return response()->json($run->fresh()->load('period'));
    }

    public function approve(PayrollRun $run, Request $request): JsonResponse
    {
        $this->workflow->approve($run, $request->user()?->id);
        return response()->json($run->fresh());
    }

    public function markPaid(PayrollRun $run): JsonResponse
    {
        $this->workflow->markPaid($run);
        return response()->json($run->fresh());
    }
}
