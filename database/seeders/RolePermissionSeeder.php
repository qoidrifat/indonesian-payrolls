<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // employees
            'employees.view', 'employees.create', 'employees.update', 'employees.delete',
            // departments / positions / structures
            'departments.manage', 'positions.manage', 'salary_structures.manage',
            // attendance / leave / overtime
            'attendance.manage', 'leave.manage', 'overtime.manage',
            // payroll
            'payroll_periods.manage', 'payroll_runs.create', 'payroll_runs.calculate',
            'payroll_runs.approve', 'payroll_runs.view',
            // payslips
            'payslips.view', 'payslips.generate',
            // configs
            'tax.configure', 'bpjs.configure',
            // reports
            'reports.view', 'reports.export',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $superAdmin = Role::findOrCreate('super_admin');
        $superAdmin->givePermissionTo(Permission::all());

        $hr = Role::findOrCreate('hr');
        $hr->givePermissionTo([
            'employees.view', 'employees.create', 'employees.update',
            'departments.manage', 'positions.manage', 'salary_structures.manage',
            'attendance.manage', 'leave.manage', 'overtime.manage',
            'payslips.view',
        ]);

        $finance = Role::findOrCreate('finance');
        $finance->givePermissionTo([
            'employees.view',
            'payroll_periods.manage', 'payroll_runs.create', 'payroll_runs.calculate',
            'payroll_runs.approve', 'payroll_runs.view',
            'payslips.view', 'payslips.generate',
            'tax.configure', 'bpjs.configure',
            'reports.view', 'reports.export',
        ]);

        Role::findOrCreate('employee');
    }
}
