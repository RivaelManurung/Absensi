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
            ['name' => 'users.view', 'description' => 'View user records.'],
            ['name' => 'users.create', 'description' => 'Create user records.'],
            ['name' => 'users.update', 'description' => 'Update user records.'],
            ['name' => 'users.delete', 'description' => 'Delete user records.'],
            ['name' => 'users.reset-password', 'description' => 'Reset user passwords.'],
            ['name' => 'employees.view', 'description' => 'View employee records.'],
            ['name' => 'employees.create', 'description' => 'Create employee records.'],
            ['name' => 'employees.update', 'description' => 'Update employee records.'],
            ['name' => 'employees.delete', 'description' => 'Delete employee records.'],
            ['name' => 'attendance.view', 'description' => 'View attendance entries.'],
            ['name' => 'attendance.create', 'description' => 'Create attendance entries.'],
            ['name' => 'attendance.approve', 'description' => 'Approve attendance entries.'],
            ['name' => 'barcodes.view', 'description' => 'View barcode records.'],
            ['name' => 'barcodes.create', 'description' => 'Generate barcode records.'],
            ['name' => 'barcodes.update', 'description' => 'Regenerate or deactivate barcode records.'],
            ['name' => 'barcodes.delete', 'description' => 'Delete barcode records.'],
            ['name' => 'activity-logs.view', 'description' => 'View system activity logs.'],
            ['name' => 'roles.view', 'description' => 'View roles.'],
            ['name' => 'permissions.view', 'description' => 'View permissions.'],
        ];

        foreach ($permissions as $permission) {
            $existing = DB::table('permissions')->where('name', $permission['name'])->first();

            if ($existing) {
                DB::table('permissions')
                    ->where('id', $existing->id)
                    ->update([
                        'description' => $permission['description'],
                        'guard_name' => 'web',
                        'updated_at' => $now,
                    ]);

                continue;
            }

            DB::table('permissions')->insert([
                'id' => (string) Str::uuid(),
                'name' => $permission['name'],
                'guard_name' => 'web',
                'description' => $permission['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
