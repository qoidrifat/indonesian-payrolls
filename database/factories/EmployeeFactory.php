<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();

        return [
            'employee_code' => 'EMP-'.Str::upper(Str::random(6)),
            'name' => "$first $last",
            'email' => Str::lower("$first.$last@aurex.test"),
            'phone' => '+62'.$this->faker->numerify('8##########'),
            'nik' => $this->faker->numerify('################'),
            'npwp' => $this->faker->numerify('##.###.###.#-###.###'),
            'bpjs_kesehatan' => $this->faker->numerify('#############'),
            'bpjs_ketenagakerjaan' => $this->faker->numerify('###############'),
            'bank_name' => $this->faker->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']),
            'bank_account_number' => $this->faker->numerify('##########'),
            'bank_account_name' => "$first $last",
            'position' => $this->faker->randomElement([
                'Backend Engineer', 'Frontend Engineer', 'Fullstack Engineer',
                'UI/UX Designer', 'Product Manager', 'DevOps Engineer',
                'QA Engineer', 'Tech Lead', 'HR Lead', 'CEO',
            ]),
            'department' => $this->faker->randomElement(['Engineering', 'Design', 'Product', 'Operations', 'HR']),
            'employment_status' => $this->faker->randomElement(['permanent', 'permanent', 'permanent', 'contract', 'probation']),
            'marital_status' => $this->faker->randomElement(['TK', 'K0', 'K1', 'K2', 'K3']),
            'join_date' => $this->faker->dateTimeBetween('-3 years', '-1 month'),
            'base_salary' => $this->faker->randomElement([5_500_000, 6_500_000, 8_000_000, 10_000_000, 12_500_000, 15_000_000, 18_000_000]),
            'is_active' => true,
            'address' => 'Bangkalan, Madura, Jawa Timur',
        ];
    }
}
