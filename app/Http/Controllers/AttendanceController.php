<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function index(Request $request): Response
    {
        $month = $request->query('month', now()->format('Y-m'));
        [$y, $m] = explode('-', $month);
        $start = Carbon::create((int) $y, (int) $m, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $employeeId = $request->query('employee_id');

        $records = Attendance::with('employee:id,name,employee_code,position,department')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId))
            ->orderByDesc('date')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Attendance/Index', [
            'records' => $records,
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name', 'employee_code']),
            'filters' => ['month' => $month, 'employee_id' => $employeeId ? (int) $employeeId : null],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:present,remote,leave,sick,absent,holiday'],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'overtime_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'location' => ['nullable', 'string', 'max:64'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Attendance::updateOrCreate(
            ['employee_id' => $data['employee_id'], 'date' => $data['date']],
            $data,
        );

        return back()->with('success', 'Attendance saved');
    }

    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return back()->with('success', 'Attendance removed');
    }
}
