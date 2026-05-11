<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\SalaryComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalaryComponentFactory extends Factory
{
    protected $model = SalaryComponent::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'type' => 'allowance',
            'name' => 'Transport Allowance',
            'amount' => 750_000,
            'is_taxable' => true,
            'is_recurring' => true,
            'notes' => null,
        ];
    }
}
