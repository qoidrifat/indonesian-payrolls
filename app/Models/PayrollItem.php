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
        'payroll_id', 'employee_id',
        'base_salary', 'allowances', 'overtime_pay', 'bonus', 'gross_salary',
        'bpjs_kesehatan_employee', 'bpjs_kesehatan_company',
        'bpjs_jht_employee', 'bpjs_jht_company',
        'bpjs_jp_employee', 'bpjs_jp_company',
        'pph21', 'other_deductions', 'total_deductions', 'net_salary',
        'working_days', 'present_days', 'overtime_minutes',
        'breakdown', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'overtime_pay' => 'decimal:2',
            'bonus' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'bpjs_kesehatan_employee' => 'decimal:2',
            'bpjs_kesehatan_company' => 'decimal:2',
            'bpjs_jht_employee' => 'decimal:2',
            'bpjs_jht_company' => 'decimal:2',
            'bpjs_jp_employee' => 'decimal:2',
            'bpjs_jp_company' => 'decimal:2',
            'pph21' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'breakdown' => 'array',
        ];
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payslip(): HasOne
    {
        return $this->hasOne(Payslip::class);
    }
}
