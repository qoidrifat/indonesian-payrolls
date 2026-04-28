<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\PayrollPeriodController;
use App\Http\Controllers\Api\V1\PayrollRunController;
use App\Http\Controllers\Api\V1\PayslipController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        Route::apiResource('employees', EmployeeController::class)->only(['index', 'show']);
        Route::apiResource('payroll-periods', PayrollPeriodController::class)->only(['index', 'show']);

        Route::get('payroll-runs', [PayrollRunController::class, 'index']);
        Route::post('payroll-runs', [PayrollRunController::class, 'store']);
        Route::get('payroll-runs/{run}', [PayrollRunController::class, 'show']);
        Route::post('payroll-runs/{run}/calculate', [PayrollRunController::class, 'calculate']);
        Route::post('payroll-runs/{run}/approve', [PayrollRunController::class, 'approve']);
        Route::post('payroll-runs/{run}/mark-paid', [PayrollRunController::class, 'markPaid']);

        Route::get('payslips/{payslip}', [PayslipController::class, 'show']);
        Route::get('payslips/{payslip}/pdf', [PayslipController::class, 'download']);
    });
});
