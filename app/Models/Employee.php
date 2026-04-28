<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'user_id', 'employee_number', 'full_name', 'email', 'phone',
        'date_of_birth', 'gender', 'marital_status', 'religion', 'citizenship',
        'department_id', 'position_id',
        'hire_date', 'end_date', 'employment_type', 'status', 'workweek',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date'     => 'date',
        'end_date'      => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function taxProfile(): HasOne
    {
        return $this->hasOne(TaxProfile::class);
    }

    public function bpjsProfile(): HasOne
    {
        return $this->hasOne(BpjsProfile::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(Deduction::class);
    }

    public function allowances(): HasMany
    {
        return $this->hasMany(Allowance::class);
    }

    public function activeSalary(?\Illuminate\Support\Carbon $on = null): ?EmployeeSalary
    {
        $on ??= now();

        return $this->salaries()
            ->where('effective_from', '<=', $on)
            ->where(function ($q) use ($on) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', $on);
            })
            ->orderByDesc('effective_from')
            ->first();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'employee_number', 'full_name', 'department_id', 'position_id',
                'employment_type', 'status', 'hire_date', 'end_date',
            ])
            ->logOnlyDirty();
    }
}
