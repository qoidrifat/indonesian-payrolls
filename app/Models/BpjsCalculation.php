<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpjsCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_item_id', 'base_wage',
        'jht_employee', 'jht_employer', 'jp_employee', 'jp_employer',
        'jkk_employer', 'jkm_employer',
        'kesehatan_employee', 'kesehatan_employer',
        'total_employee', 'total_employer',
        'rates_snapshot',
    ];

    protected $casts = [
        'rates_snapshot'      => 'array',
        'base_wage'           => 'decimal:2',
        'jht_employee'        => 'decimal:2',
        'jht_employer'        => 'decimal:2',
        'jp_employee'         => 'decimal:2',
        'jp_employer'         => 'decimal:2',
        'jkk_employer'        => 'decimal:2',
        'jkm_employer'        => 'decimal:2',
        'kesehatan_employee'  => 'decimal:2',
        'kesehatan_employer'  => 'decimal:2',
        'total_employee'      => 'decimal:2',
        'total_employer'      => 'decimal:2',
    ];

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }
}
