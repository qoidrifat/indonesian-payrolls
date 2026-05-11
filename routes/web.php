<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryComponentController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('employees', EmployeeController::class);

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    Route::get('/salary-components', [SalaryComponentController::class, 'index'])->name('salary-components.index');
    Route::post('/salary-components', [SalaryComponentController::class, 'store'])->name('salary-components.store');
    Route::patch('/salary-components/{salary_component}', [SalaryComponentController::class, 'update'])->name('salary-components.update');
    Route::delete('/salary-components/{salary_component}', [SalaryComponentController::class, 'destroy'])->name('salary-components.destroy');

    Route::get('/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('/payrolls/create', [PayrollController::class, 'create'])->name('payrolls.create');
    Route::post('/payrolls', [PayrollController::class, 'store'])->name('payrolls.store');
    Route::get('/payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::post('/payrolls/{payroll}/run', [PayrollController::class, 'run'])->name('payrolls.run');
    Route::post('/payrolls/{payroll}/approve', [PayrollController::class, 'approve'])->name('payrolls.approve');
    Route::post('/payrolls/{payroll}/pay', [PayrollController::class, 'pay'])->name('payrolls.pay');
    Route::delete('/payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');

    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('/payslips/item/{item}', [PayslipController::class, 'show'])
        ->whereNumber('item')
        ->scopeBindings()
        ->name('payslips.show');
    Route::post('/payslips/payroll/{payroll}/generate', [PayslipController::class, 'generate'])->name('payslips.generate');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/payroll/{payroll}/excel', [ReportController::class, 'payrollExcel'])->name('reports.payroll.excel');
    Route::get('/reports/payroll/{payroll}/pdf', [ReportController::class, 'payrollPdf'])->name('reports.payroll.pdf');
    Route::get('/reports/tax/excel', [ReportController::class, 'taxExcel'])->name('reports.tax.excel');
    Route::get('/reports/attendance/excel', [ReportController::class, 'attendanceExcel'])->name('reports.attendance.excel');
    Route::get('/reports/employee/{employee}/history', [ReportController::class, 'employeeHistory'])->name('reports.employee.history');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
