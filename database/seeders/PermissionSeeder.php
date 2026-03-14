<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $permissions = [
            ['name' => 'attendance.create', 'description' => 'Create attendance entries.'],
            ['name' => 'attendance.view', 'description' => 'View attendance entries.'],
            ['name' => 'attendance.approve', 'description' => 'Approve attendance entries.'],
            ['name' => 'employee.create', 'description' => 'Create employee records.'],
            ['name' => 'employee.update', 'description' => 'Update employee records.'],
            ['name' => 'employee.delete', 'description' => 'Delete employee records.'],
            ['name' => 'report.view', 'description' => 'View attendance and HR reports.'],
        ];

        foreach ($permissions as $permission) {
            $existing = DB::table('permissions')->where('name', $permission['name'])->first();

            if ($existing) {
                DB::table('permissions')
                    ->where('id', $existing->id)
                    ->update(['description' => $permission['description']]);

                continue;
            }

            DB::table('permissions')->insert([
                'id' => (string) Str::uuid(),
                'name' => $permission['name'],
                'description' => $permission['description'],
                'created_at' => $now,
            ]);
        }
    }
}
