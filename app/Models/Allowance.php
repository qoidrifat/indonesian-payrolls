<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'payroll_period_id', 'name', 'amount', 'type',
        'is_taxable', 'notes',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'is_taxable' => 'bool',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
}
