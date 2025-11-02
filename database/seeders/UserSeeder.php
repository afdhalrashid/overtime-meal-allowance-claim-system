<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create HR Admin
        \App\Models\User::firstOrCreate(
            ['email' => 'hr@company.edu'],
            [
                'name' => 'HR Administrator',
                'employee_id' => 'HR001',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'hr_admin',
                'department_id' => 1, // HR Department
                'phone' => '+60123456789',
                'is_active' => true,
            ]
        );

        // Create Payroll Staff
        \App\Models\User::firstOrCreate(
            ['email' => 'payroll@company.edu'],
            [
                'name' => 'Payroll Staff',
                'employee_id' => 'HR002',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'payroll',
                'department_id' => 1, // HR Department
                'phone' => '+60123456790',
                'is_active' => true,
            ]
        );

        // Create IT Manager (Approver)
        \App\Models\User::firstOrCreate(
            ['email' => 'it.manager@company.edu'],
            [
                'name' => 'IT Manager',
                'employee_id' => 'IT001',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'approver',
                'department_id' => 2, // IT Department
                'phone' => '+60123456791',
                'is_active' => true,
            ]
        );

        // Create IT Staff
        \App\Models\User::firstOrCreate(
            ['email' => 'it.staff@company.edu'],
            [
                'name' => 'IT Staff',
                'employee_id' => 'IT002',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'staff',
                'department_id' => 2, // IT Department
                'manager_id' => 3, // IT Manager
                'phone' => '+60123456792',
                'involves_driving' => false,
                'is_active' => true,
            ]
        );

        // Create Finance Manager (Approver)
        \App\Models\User::firstOrCreate(
            ['email' => 'finance.manager@company.edu'],
            [
                'name' => 'Finance Manager',
                'employee_id' => 'FIN001',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'approver',
                'department_id' => 3, // Finance Department
                'phone' => '+60123456793',
                'is_active' => true,
            ]
        );

        // Create Operations Staff with driving duties
        \App\Models\User::firstOrCreate(
            ['email' => 'ops.staff@company.edu'],
            [
                'name' => 'Operations Staff',
                'employee_id' => 'OPS001',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'staff',
                'department_id' => 4, // Operations Department
                'manager_id' => 5, // Finance Manager (acting as approver)
                'phone' => '+60123456794',
                'involves_driving' => true,
                'is_active' => true,
            ]
        );
    }
}
