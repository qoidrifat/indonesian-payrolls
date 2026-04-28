<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_structure_id', 'code', 'name', 'type', 'category', 'amount',
        'is_taxable', 'counts_toward_bpjs', 'prorate_on_attendance', 'sort_order',
    ];

    protected $casts = [
        'amount'                 => 'decimal:2',
        'is_taxable'             => 'bool',
        'counts_toward_bpjs'     => 'bool',
        'prorate_on_attendance'  => 'bool',
    ];

    public function structure(): BelongsTo
    {
        return $this->belongsTo(SalaryStructure::class, 'salary_structure_id');
    }
}
