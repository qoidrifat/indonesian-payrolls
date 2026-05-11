<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SalaryComponentController extends Controller
{
    public function index(Request $request): Response
    {
        $employeeId = $request->query('employee_id');

        $components = SalaryComponent::with('employee:id,name,employee_code')
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('SalaryConfiguration/Index', [
            'components' => $components,
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name', 'employee_code', 'base_salary']),
            'filters' => ['employee_id' => $employeeId ? (int) $employeeId : null],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        SalaryComponent::create($data);

        return back()->with('success', 'Component saved');
    }

    public function update(Request $request, SalaryComponent $salaryComponent): RedirectResponse
    {
        $salaryComponent->update($this->validated($request));

        return back()->with('success', 'Component updated');
    }

    public function destroy(SalaryComponent $salaryComponent): RedirectResponse
    {
        $salaryComponent->delete();

        return back()->with('success', 'Component removed');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'type' => ['required', 'in:allowance,deduction,bonus'],
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'is_taxable' => ['required', 'boolean'],
            'is_recurring' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
