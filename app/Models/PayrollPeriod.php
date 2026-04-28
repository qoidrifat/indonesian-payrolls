<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'year', 'month', 'start_date', 'end_date',
        'payment_date', 'status', 'is_thr_period',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'payment_date'  => 'date',
        'is_thr_period' => 'bool',
    ];

    public function runs(): HasMany
    {
        return $this->hasMany(PayrollRun::class);
    }
}
