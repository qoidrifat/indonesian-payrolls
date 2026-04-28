<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalary extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_id', 'salary_structure_id',
        'basic_salary', 'fixed_allowance_total', 'variable_allowance_total',
        'effective_from', 'effective_to', 'currency', 'notes',
    ];

    protected $casts = [
        'basic_salary'              => 'decimal:2',
        'fixed_allowance_total'     => 'decimal:2',
        'variable_allowance_total'  => 'decimal:2',
        'effective_from'            => 'date',
        'effective_to'              => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(SalaryStructure::class, 'salary_structure_id');
    }

    public function bpjsBaseWage(): float
    {
        return (float) $this->basic_salary + (float) $this->fixed_allowance_total;
    }

    public function grossMonthly(): float
    {
        return $this->bpjsBaseWage() + (float) $this->variable_allowance_total;
    }
}
