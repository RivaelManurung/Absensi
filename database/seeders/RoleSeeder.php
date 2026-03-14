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
            $existing = DB::table('roles')->where('name', $role['name'])->first();

            if ($existing) {
                DB::table('roles')
                    ->where('id', $existing->id)
                    ->update(['description' => $role['description']]);

                continue;
            }

            DB::table('roles')->insert([
                'id' => (string) Str::uuid(),
                'name' => $role['name'],
                'description' => $role['description'],
                'created_at' => $now,
            ]);
        }
    }
}
