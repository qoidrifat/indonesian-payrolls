<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_item_id', 'employee_id', 'payroll_period_id',
        'payslip_number', 'pdf_path', 'pdf_disk', 'snapshot',
        'generated_at', 'sent_at',
    ];

    protected $casts = [
        'snapshot'     => 'array',
        'generated_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
