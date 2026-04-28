<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'has_npwp', 'npwp_number', 'ptkp_status', 'ter_category',
        'is_foreign_tax_resident',
    ];

    protected $casts = [
        'has_npwp'                => 'bool',
        'is_foreign_tax_resident' => 'bool',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function resolvedTerCategory(): string
    {
        return $this->ter_category
            ?? config("payroll.ptkp.ter_category.{$this->ptkp_status}", 'A');
    }
}
