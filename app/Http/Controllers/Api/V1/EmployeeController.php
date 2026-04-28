<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            Employee::query()
                ->with(['department', 'position'])
                ->paginate($request->integer('per_page', 25))
        );
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json(
            $employee->load(['department', 'position', 'profile', 'taxProfile', 'bpjsProfile'])
        );
    }
}
