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
                'attendance.create',
                'attendance.view',
                'attendance.approve',
                'employee.create',
                'employee.update',
                'employee.delete',
                'report.view',
            ],
            'HR' => [
                'attendance.view',
                'attendance.approve',
                'employee.create',
                'employee.update',
                'report.view',
            ],
            'Employee' => [
                'attendance.create',
                'attendance.view',
            ],
            'Supervisor' => [
                'attendance.view',
                'attendance.approve',
                'report.view',
            ],
            'Manager' => [
                'attendance.view',
                'attendance.approve',
                'report.view',
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
            }
        }
    }
}
