<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\TaxProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxProfileFactory extends Factory
{
    protected $model = TaxProfile::class;

    public function definition(): array
    {
        return [
            'employee_id'             => Employee::factory(),
            'has_npwp'                => true,
            'npwp_number'             => $this->faker->numerify('##.###.###.#-###.###'),
            'ptkp_status'             => 'TK/0',
            'ter_category'            => 'A',
            'is_foreign_tax_resident' => false,
        ];
    }
}
