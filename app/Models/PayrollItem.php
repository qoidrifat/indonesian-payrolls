<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id', 'employee_id',
        'basic_salary', 'fixed_allowance', 'variable_allowance',
        'overtime_amount', 'thr_amount', 'bonus_amount', 'other_earnings',
        'gross_salary', 'bpjs_employee_total', 'bpjs_employer_total',
        'pph21_amount', 'other_deductions', 'total_deductions', 'net_salary',
        'breakdown', 'idempotency_key',
    ];

    protected $casts = [
        'breakdown'           => 'array',
        'basic_salary'        => 'decimal:2',
        'fixed_allowance'     => 'decimal:2',
        'variable_allowance'  => 'decimal:2',
        'overtime_amount'     => 'decimal:2',
        'thr_amount'          => 'decimal:2',
        'bonus_amount'        => 'decimal:2',
        'other_earnings'      => 'decimal:2',
        'gross_salary'        => 'decimal:2',
        'bpjs_employee_total' => 'decimal:2',
        'bpjs_employer_total' => 'decimal:2',
        'pph21_amount'        => 'decimal:2',
        'other_deductions'    => 'decimal:2',
        'total_deductions'    => 'decimal:2',
        'net_salary'          => 'decimal:2',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function taxCalculation(): HasOne
    {
        return $this->hasOne(TaxCalculation::class);
    }

    public function bpjsCalculation(): HasOne
    {
        return $this->hasOne(BpjsCalculation::class);
    }

    public function payslip(): HasOne
    {
        return $this->hasOne(Payslip::class);
    }
}
