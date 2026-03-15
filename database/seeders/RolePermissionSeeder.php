<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'name');
        $permissions = DB::table('permissions')->pluck('id', 'name');

        $matrix = [
            'Admin' => [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'users.reset-password',
                'employees.view',
                'employees.create',
                'employees.update',
                'employees.delete',
                'attendance.create',
                'attendance.view',
                'attendance.approve',
                'barcodes.view',
                'barcodes.create',
                'barcodes.update',
                'barcodes.delete',
                'activity-logs.view',
                'roles.view',
                'permissions.view',
            ],
            'HR' => [
                'users.view',
                'users.create',
                'users.update',
                'users.reset-password',
                'employees.view',
                'employees.create',
                'employees.update',
                'attendance.view',
                'attendance.approve',
                'barcodes.view',
                'barcodes.create',
                'barcodes.update',
                'activity-logs.view',
            ],
            'Employee' => [
                'attendance.create',
                'attendance.view',
            ],
            'Supervisor' => [
                'attendance.view',
                'attendance.approve',
                'barcodes.view',
            ],
            'Manager' => [
                'employees.view',
                'attendance.view',
                'attendance.approve',
                'barcodes.view',
                'activity-logs.view',
            ],
        ];

        foreach ($matrix as $roleName => $permissionNames) {
            $roleId = $roles[$roleName] ?? null;

            if (! $roleId) {
                continue;
            }

            foreach ($permissionNames as $permissionName) {
                $permissionId = $permissions[$permissionName] ?? null;

                if (! $permissionId) {
                    continue;
                }

                $exists = DB::table('role_permissions')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (! $exists) {
                    DB::table('role_permissions')->insert([
                        'id' => (string) Str::uuid(),
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }

                $spatieExists = DB::table('role_has_permissions')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (! $spatieExists) {
                    DB::table('role_has_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }
}
