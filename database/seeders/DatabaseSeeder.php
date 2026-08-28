<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
        ]);

        // ---- Internal users (admin & receptionist) ----
        User::updateOrCreate(
            ['email' => 'admin@pelindo.id'],
            [
                'name' => 'Admin SDM & Umum',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'receptionist@pelindo.id'],
            [
                'name' => 'Resepsionis Lobi',
                'password' => 'password',
                'role' => 'receptionist',
                'email_verified_at' => now(),
            ]
        );

        // ---- Sample employees (master data) ----
        $employees = [
            ['name' => 'Budi Santoso', 'department' => 'Divisi Umum', 'position' => 'Kepala Divisi', 'is_active' => true],
            ['name' => 'Siti Rahma', 'department' => 'SDM', 'position' => 'Staf HRD', 'is_active' => true],
            ['name' => 'Andi Wijaya', 'department' => 'IT', 'position' => 'System Analyst', 'is_active' => true],
            ['name' => 'Dewi Lestari', 'department' => 'Keuangan', 'position' => 'Finance Officer', 'is_active' => true],
            ['name' => 'Rian Fahmi', 'department' => 'Operasional', 'position' => 'Supervisor Lapangan', 'is_active' => true],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(
                ['name' => $employee['name']],
                $employee
            );
        }
    }
}
