<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'work_date', 'check_in', 'check_out',
        'worked_hours', 'status', 'is_overtime_eligible', 'notes',
    ];

    protected $casts = [
        'work_date'            => 'date',
        'check_in'             => 'datetime',
        'check_out'            => 'datetime',
        'worked_hours'         => 'decimal:2',
        'is_overtime_eligible' => 'bool',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
