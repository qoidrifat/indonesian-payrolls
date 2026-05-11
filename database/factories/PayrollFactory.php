<?php

namespace Database\Factories;

use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition(): array
    {
        $month = Carbon::now()->subMonth();

        return [
            'code' => 'PR-'.$month->format('Ym'),
            'period_year' => $month->year,
            'period_month' => $month->month,
            'period_start' => $month->copy()->startOfMonth()->toDateString(),
            'period_end' => $month->copy()->endOfMonth()->toDateString(),
            'pay_date' => $month->copy()->endOfMonth()->day(25)->toDateString(),
            'status' => 'draft',
        ];
    }
}
