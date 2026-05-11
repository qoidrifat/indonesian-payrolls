<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['present', 'present', 'present', 'remote', 'remote', 'leave', 'sick']);

        return [
            'employee_id' => Employee::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => $status,
            'check_in' => in_array($status, ['present', 'remote']) ? '09:00:00' : null,
            'check_out' => in_array($status, ['present', 'remote']) ? '18:00:00' : null,
            'overtime_minutes' => $this->faker->randomElement([0, 0, 0, 30, 60, 90]),
            'location' => $status === 'remote' ? 'Home' : 'Office',
            'notes' => null,
        ];
    }
}
