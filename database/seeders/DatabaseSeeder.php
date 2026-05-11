<?php

namespace Database\Seeders;

use App\Actions\Payroll\RunPayroll;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryComponent;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedSettings();
        $this->seedUsers();
        $this->seedEmployees();
        $this->seedAttendance();
        $this->seedPayrolls();
    }

    protected function seedRoles(): void
    {
        $permissions = [
            'employees.view', 'employees.manage',
            'attendance.view', 'attendance.manage',
            'payroll.view', 'payroll.manage', 'payroll.approve',
            'reports.view', 'settings.manage',
        ];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $hr = Role::firstOrCreate(['name' => 'hr']);
        $hr->syncPermissions([
            'employees.view', 'employees.manage',
            'attendance.view', 'attendance.manage',
            'payroll.view', 'payroll.manage',
            'reports.view',
        ]);

        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->syncPermissions(['attendance.view', 'payroll.view']);
    }

    protected function seedSettings(): void
    {
        $defaults = [
            ['company_name', 'Aurex Payroll', 'string', 'company'],
            ['company_address', 'Jl. Trunojoyo No. 1, Kabupaten Bangkalan, Jawa Timur', 'string', 'company'],
            ['company_email', 'hello@aurex.id', 'string', 'company'],
            ['company_phone', '+62 31 1234567', 'string', 'company'],
            ['pay_day', 25, 'int', 'payroll'],
            ['currency', 'IDR', 'string', 'payroll'],
            ['default_working_hours', 8, 'int', 'payroll'],
        ];
        foreach ($defaults as [$k, $v, $t, $g]) {
            Setting::set($k, $v, $t, $g);
        }
    }

    protected function seedUsers(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@aurex.test'],
            ['name' => 'Aurex Admin', 'password' => bcrypt('password'), 'email_verified_at' => now()],
        );
        $admin->syncRoles('admin');

        $hr = User::firstOrCreate(
            ['email' => 'hr@aurex.test'],
            ['name' => 'Sari HR', 'password' => bcrypt('password'), 'email_verified_at' => now()],
        );
        $hr->syncRoles('hr');
    }

    protected function seedEmployees(): void
    {
        if (Employee::count() > 0) {
            return;
        }

        $blueprints = [
            ['Hafiz Pratama',       'Tech Lead',         'Engineering', 18_000_000, 'K2'],
            ['Rina Setiawati',      'HR Lead',           'HR',          12_500_000, 'K1'],
            ['Bagus Wicaksono',     'Backend Engineer',  'Engineering', 10_000_000, 'TK'],
            ['Dewi Anggraini',      'Frontend Engineer', 'Engineering', 9_500_000,  'TK'],
            ['Fikri Maulana',       'Fullstack Engineer', 'Engineering', 11_000_000, 'K0'],
            ['Naufal Iskandar',     'DevOps Engineer',   'Engineering', 12_000_000, 'K0'],
            ['Salwa Kirana',        'UI/UX Designer',    'Design',      8_500_000,  'TK'],
            ['Damar Adi',           'Product Manager',   'Product',     14_000_000, 'K1'],
            ['Tasya Maharani',      'QA Engineer',       'Engineering', 8_000_000,  'TK'],
            ['Reza Pahlevi',        'Operations',        'Operations',  7_500_000,  'K0'],
        ];

        foreach ($blueprints as $i => [$name, $position, $department, $salary, $ptkp]) {
            $first = strtolower(explode(' ', $name)[0]);
            Employee::create([
                'employee_code' => sprintf('AUR-%03d', $i + 1),
                'name' => $name,
                'email' => $first.'@aurex.test',
                'phone' => '+62 8'.str_pad((string) rand(1, 999999999), 9, '0', STR_PAD_LEFT),
                'nik' => str_pad((string) rand(1, PHP_INT_MAX), 16, '0', STR_PAD_LEFT),
                'npwp' => $i % 5 === 4 ? null : sprintf('%08d-%03d.000', rand(10000000, 99999999), rand(100, 999)),
                'bpjs_kesehatan' => sprintf('%013d', rand(1, PHP_INT_MAX)),
                'bpjs_ketenagakerjaan' => sprintf('%015d', rand(1, PHP_INT_MAX)),
                'bank_name' => ['BCA', 'Mandiri', 'BNI', 'BRI'][$i % 4],
                'bank_account_number' => str_pad((string) rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
                'bank_account_name' => $name,
                'position' => $position,
                'department' => $department,
                'employment_status' => 'permanent',
                'marital_status' => $ptkp,
                'join_date' => Carbon::now()->subMonths(rand(3, 30))->toDateString(),
                'base_salary' => $salary,
                'is_active' => true,
                'address' => 'Bangkalan, Madura, Jawa Timur',
            ]);
        }

        // Add a transport allowance for each employee
        Employee::all()->each(function (Employee $e) {
            SalaryComponent::create([
                'employee_id' => $e->id,
                'type' => 'allowance',
                'name' => 'Transport & Meal',
                'amount' => 750_000,
                'is_taxable' => true,
                'is_recurring' => true,
            ]);
            if ($e->position === 'Tech Lead' || $e->position === 'Product Manager') {
                SalaryComponent::create([
                    'employee_id' => $e->id,
                    'type' => 'allowance',
                    'name' => 'Leadership Allowance',
                    'amount' => 1_500_000,
                    'is_taxable' => true,
                    'is_recurring' => true,
                ]);
            }
        });
    }

    protected function seedAttendance(): void
    {
        $employees = Employee::all();
        $start = Carbon::now()->startOfMonth();
        $today = Carbon::now();

        foreach ($employees as $employee) {
            for ($d = $start->copy(); $d->lte($today); $d->addDay()) {
                if ($d->isWeekend()) {
                    continue;
                }
                $rand = rand(1, 100);
                $status = match (true) {
                    $rand <= 60 => 'present',
                    $rand <= 85 => 'remote',
                    $rand <= 92 => 'leave',
                    $rand <= 97 => 'sick',
                    default => 'absent',
                };
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $d->toDateString(),
                    'status' => $status,
                    'check_in' => in_array($status, ['present', 'remote']) ? '09:00:00' : null,
                    'check_out' => in_array($status, ['present', 'remote']) ? '18:00:00' : null,
                    'overtime_minutes' => rand(0, 100) > 85 ? rand(30, 120) : 0,
                    'location' => $status === 'remote' ? 'Home' : ($status === 'present' ? 'Office' : null),
                ]);
            }
        }
    }

    protected function seedPayrolls(): void
    {
        $month = Carbon::now()->subMonth();
        $payroll = Payroll::firstOrCreate([
            'period_year' => $month->year,
            'period_month' => $month->month,
        ], [
            'code' => 'PR-'.$month->format('Ym'),
            'period_start' => $month->copy()->startOfMonth()->toDateString(),
            'period_end' => $month->copy()->endOfMonth()->toDateString(),
            'pay_date' => $month->copy()->endOfMonth()->day(25)->toDateString(),
            'status' => 'draft',
            'notes' => 'Seeded sample payroll',
        ]);

        (new RunPayroll)->execute($payroll);
        $payroll->update(['status' => 'paid']);
    }
}
