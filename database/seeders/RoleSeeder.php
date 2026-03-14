<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $roles = [
            ['name' => 'Admin', 'description' => 'Full system administrator access.'],
            ['name' => 'HR', 'description' => 'Human resources management access.'],
            ['name' => 'Employee', 'description' => 'Standard employee self-service access.'],
            ['name' => 'Supervisor', 'description' => 'Team-level supervision and approvals.'],
            ['name' => 'Manager', 'description' => 'Department-level management and reporting.'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'id' => (string) Str::uuid(),
                    'description' => $role['description'],
                    'created_at' => $now,
                ]
            );
        }
    }
}
