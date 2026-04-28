<?php

namespace Database\Seeders;

use App\Models\BpjsProfile;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\TaxProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.test'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')],
        );
        $admin->syncRoles(['super_admin']);

        $hr = User::firstOrCreate(
            ['email' => 'hr@example.test'],
            ['name' => 'HR Manager', 'password' => Hash::make('password')],
        );
        $hr->syncRoles(['hr']);

        $finance = User::firstOrCreate(
            ['email' => 'finance@example.test'],
            ['name' => 'Finance Officer', 'password' => Hash::make('password')],
        );
        $finance->syncRoles(['finance']);

        $eng = Department::firstOrCreate(
            ['code' => 'ENG'],
            ['name' => 'Engineering'],
        );

        $position = Position::firstOrCreate(
            ['code' => 'SE'],
            ['name' => 'Software Engineer', 'department_id' => $eng->id, 'level' => 'staff'],
        );

        $employee = Employee::firstOrCreate(
            ['employee_number' => 'EMP-0001'],
            [
                'full_name'       => 'Budi Santoso',
                'email'           => 'budi@example.test',
                'department_id'   => $eng->id,
                'position_id'     => $position->id,
                'hire_date'       => now()->startOfYear(),
                'employment_type' => 'permanent',
                'status'          => 'active',
                'workweek'        => 'five_days',
                'marital_status'  => 'single',
            ],
        );

        TaxProfile::updateOrCreate(
            ['employee_id' => $employee->id],
            ['has_npwp' => true, 'ptkp_status' => 'TK/0', 'ter_category' => 'A'],
        );

        BpjsProfile::updateOrCreate(
            ['employee_id' => $employee->id],
            ['enroll_jht' => true, 'enroll_jp' => true, 'enroll_jkk' => true, 'enroll_jkm' => true, 'enroll_kesehatan' => true, 'jkk_risk_grade' => 'I'],
        );

        EmployeeSalary::firstOrCreate(
            ['employee_id' => $employee->id, 'effective_from' => now()->startOfYear()->toDateString()],
            [
                'basic_salary'             => 8_000_000,
                'fixed_allowance_total'    => 1_000_000,
                'variable_allowance_total' => 500_000,
                'currency'                 => 'IDR',
            ],
        );

        $year = (int) now()->year;
        for ($m = 1; $m <= 12; $m++) {
            PayrollPeriod::firstOrCreate(
                ['year' => $year, 'month' => $m],
                [
                    'code'         => sprintf('%04d-%02d', $year, $m),
                    'start_date'   => sprintf('%04d-%02d-01', $year, $m),
                    'end_date'     => date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $year, $m))),
                    'payment_date' => date('Y-m-25', strtotime(sprintf('%04d-%02d-01', $year, $m))),
                    'status'       => 'open',
                ]
            );
        }
    }
}
