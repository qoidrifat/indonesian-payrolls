<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_number' => 'EMP-' . str_pad((string) $this->faker->unique()->randomNumber(5), 5, '0', STR_PAD_LEFT),
            'full_name'       => $this->faker->name(),
            'email'           => $this->faker->unique()->safeEmail(),
            'phone'           => $this->faker->phoneNumber(),
            'date_of_birth'   => $this->faker->date('Y-m-d', '-25 years'),
            'gender'          => $this->faker->randomElement(['male', 'female']),
            'marital_status'  => $this->faker->randomElement(['single', 'married']),
            'religion'        => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'citizenship'     => 'WNI',
            'department_id'   => null,
            'position_id'     => null,
            'hire_date'       => $this->faker->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d'),
            'employment_type' => 'permanent',
            'status'          => 'active',
            'workweek'        => 'five_days',
        ];
    }
}
