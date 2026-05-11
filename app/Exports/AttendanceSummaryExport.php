<?php

namespace App\Exports;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class AttendanceSummaryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    public function __construct(public string $month) {}

    public function collection(): Collection
    {
        [$y, $m] = explode('-', $this->month);
        $start = Carbon::create((int) $y, (int) $m, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        return Attendance::query()
            ->join('employees', 'employees.id', '=', 'attendances.employee_id')
            ->whereBetween('attendances.date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('employees.employee_code, employees.name, '.
                'SUM(CASE WHEN attendances.status="present" THEN 1 ELSE 0 END) as present_count, '.
                'SUM(CASE WHEN attendances.status="remote" THEN 1 ELSE 0 END) as remote_count, '.
                'SUM(CASE WHEN attendances.status="leave" THEN 1 ELSE 0 END) as leave_count, '.
                'SUM(CASE WHEN attendances.status="sick" THEN 1 ELSE 0 END) as sick_count, '.
                'SUM(CASE WHEN attendances.status="absent" THEN 1 ELSE 0 END) as absent_count, '.
                'SUM(attendances.overtime_minutes) as overtime_minutes')
            ->groupBy('employees.id', 'employees.employee_code', 'employees.name')
            ->orderBy('employees.name')
            ->get();
    }

    public function headings(): array
    {
        return ['Code', 'Name', 'Present', 'Remote', 'Leave', 'Sick', 'Absent', 'Overtime (min)'];
    }

    public function map($row): array
    {
        return [
            $row->employee_code, $row->name,
            (int) $row->present_count, (int) $row->remote_count,
            (int) $row->leave_count, (int) $row->sick_count,
            (int) $row->absent_count, (int) $row->overtime_minutes,
        ];
    }

    public function title(): string
    {
        return 'Attendance '.$this->month;
    }
}
