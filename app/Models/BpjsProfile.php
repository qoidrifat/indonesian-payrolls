<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpjsProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'ketenagakerjaan_number', 'kesehatan_number',
        'enroll_jht', 'enroll_jp', 'enroll_jkk', 'enroll_jkm', 'enroll_kesehatan',
        'jkk_risk_grade',
    ];

    protected $casts = [
        'enroll_jht'       => 'bool',
        'enroll_jp'        => 'bool',
        'enroll_jkk'       => 'bool',
        'enroll_jkm'       => 'bool',
        'enroll_kesehatan' => 'bool',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
