<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PayrollRun extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'payroll_period_id', 'name', 'run_type', 'status',
        'employee_count', 'total_gross', 'total_net', 'total_tax',
        'total_bpjs_employee', 'total_bpjs_employer',
        'created_by', 'approved_by', 'approved_at', 'calculated_at', 'paid_at', 'meta',
    ];

    protected $casts = [
        'meta'          => 'array',
        'approved_at'   => 'datetime',
        'calculated_at' => 'datetime',
        'paid_at'       => 'datetime',
        'total_gross'         => 'decimal:2',
        'total_net'           => 'decimal:2',
        'total_tax'           => 'decimal:2',
        'total_bpjs_employee' => 'decimal:2',
        'total_bpjs_employer' => 'decimal:2',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['payroll_period_id', 'name', 'run_type', 'status'])
            ->logOnlyDirty();
    }
}
