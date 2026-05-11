<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function index(Request $request): Response
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $employees = Employee::query()
            ->when($q !== '', fn ($x) => $x->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('employee_code', 'like', "%$q%")
                    ->orWhere('position', 'like', "%$q%");
            }))
            ->when($status === 'active', fn ($x) => $x->where('is_active', true))
            ->when($status === 'inactive', fn ($x) => $x->where('is_active', false))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Employees/Index', [
            'employees' => $employees,
            'filters' => ['q' => $q, 'status' => $status],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Employees/Form', [
            'employee' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $employee = Employee::create($data);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee created');
    }

    public function show(Employee $employee): Response
    {
        $employee->load(['salaryComponents', 'payrollItems.payroll', 'attendances' => fn ($q) => $q->latest('date')->limit(30)]);

        return Inertia::render('Employees/Show', [
            'employee' => $employee,
        ]);
    }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('Employees/Form', [
            'employee' => $employee,
        ]);
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $this->validated($request, $employee->id);
        $employee->update($data);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee archived');
    }

    protected function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'employee_code' => ['required', 'string', 'max:32', 'unique:employees,employee_code'.($id ? ",$id" : '')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'.($id ? ",$id" : '')],
            'phone' => ['nullable', 'string', 'max:32'],
            'nik' => ['nullable', 'string', 'max:32'],
            'npwp' => ['nullable', 'string', 'max:32'],
            'bpjs_kesehatan' => ['nullable', 'string', 'max:32'],
            'bpjs_ketenagakerjaan' => ['nullable', 'string', 'max:32'],
            'bank_name' => ['nullable', 'string', 'max:64'],
            'bank_account_number' => ['nullable', 'string', 'max:64'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:64'],
            'employment_status' => ['required', 'in:permanent,contract,probation,intern'],
            'marital_status' => ['required', 'in:TK,K0,K1,K2,K3'],
            'join_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:join_date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
