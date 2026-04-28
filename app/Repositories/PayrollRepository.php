<?php

namespace App\Repositories;

use App\Models\PayrollItem;
use App\Models\PayrollRun;
use Illuminate\Database\Eloquent\Collection;

class PayrollRepository
{
    public function findRun(int $id): ?PayrollRun
    {
        return PayrollRun::query()->with('period')->find($id);
    }

    public function itemsForRun(PayrollRun $run): Collection
    {
        return $run->items()
            ->with(['employee', 'taxCalculation', 'bpjsCalculation'])
            ->get();
    }

    public function itemsForEmployeeAndYear(int $employeeId, int $year): Collection
    {
        return PayrollItem::query()
            ->where('employee_id', $employeeId)
            ->whereHas('run.period', fn ($q) => $q->where('year', $year))
            ->with(['run.period', 'taxCalculation', 'bpjsCalculation'])
            ->get();
    }
}
