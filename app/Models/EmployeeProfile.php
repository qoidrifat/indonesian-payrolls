<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'ktp_number', 'npwp_number', 'passport_number',
        'address_line1', 'address_line2', 'city', 'province', 'postal_code',
        'bank_name', 'bank_account_number', 'bank_account_holder',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
