<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollPeriodController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            PayrollPeriod::query()
                ->orderByDesc('year')->orderByDesc('month')
                ->paginate($request->integer('per_page', 25))
        );
    }

    public function show(PayrollPeriod $payrollPeriod): JsonResponse
    {
        return response()->json($payrollPeriod->load('runs'));
    }
}
