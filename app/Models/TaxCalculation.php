<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_item_id',
        'method', 'ter_category', 'ptkp_status', 'ptkp_amount',
        'gross_taxable', 'biaya_jabatan', 'jht_jp_employee', 'net_taxable', 'pkp',
        'ter_rate', 'pph21_amount',
        'ytd_gross', 'ytd_pph21',
        'non_npwp_surcharge_applied',
    ];

    protected $casts = [
        'ptkp_amount'                => 'decimal:2',
        'gross_taxable'              => 'decimal:2',
        'biaya_jabatan'              => 'decimal:2',
        'jht_jp_employee'            => 'decimal:2',
        'net_taxable'                => 'decimal:2',
        'pkp'                        => 'decimal:2',
        'ter_rate'                   => 'decimal:6',
        'pph21_amount'               => 'decimal:2',
        'ytd_gross'                  => 'decimal:2',
        'ytd_pph21'                  => 'decimal:2',
        'non_npwp_surcharge_applied' => 'bool',
    ];

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }
}
