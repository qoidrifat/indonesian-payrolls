<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class EmployeeRepository
{
    public function activeForPeriod(Carbon $start, Carbon $end): Collection
    {
        return Employee::query()
            ->where('status', 'active')
            ->where('hire_date', '<=', $end)
            ->where(function ($q) use ($start) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $start);
            })
            ->with(['taxProfile', 'bpjsProfile'])
            ->get();
    }
}
