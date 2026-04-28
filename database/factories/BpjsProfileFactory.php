<?php

namespace Database\Factories;

use App\Models\BpjsProfile;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class BpjsProfileFactory extends Factory
{
    protected $model = BpjsProfile::class;

    public function definition(): array
    {
        return [
            'employee_id'      => Employee::factory(),
            'enroll_jht'       => true,
            'enroll_jp'        => true,
            'enroll_jkk'       => true,
            'enroll_jkm'       => true,
            'enroll_kesehatan' => true,
            'jkk_risk_grade'   => 'I',
        ];
    }
}
