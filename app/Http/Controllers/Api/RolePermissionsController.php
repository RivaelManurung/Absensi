<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RolePermissionsController extends BaseCrudController
{
    protected string $table = 'role_permissions';

    protected array $fillable = [
        'role_id',
        'permission_id',
    ];

    protected function storeRules(): array
    {
        return [
            'role_id' => ['required', 'uuid', 'exists:roles,id'],
            'permission_id' => ['required', 'uuid', 'exists:permissions,id'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'role_id' => ['sometimes', 'uuid', 'exists:roles,id'],
            'permission_id' => ['sometimes', 'uuid', 'exists:permissions,id'],
        ];
    }

    protected function mutateBeforeStore(array $data): array
    {
        if (isset($data['role_id'], $data['permission_id'])) {
            $exists = DB::table($this->table)
                ->where('role_id', $data['role_id'])
                ->where('permission_id', $data['permission_id'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'role_permission' => ['Role and permission pair already exists.'],
                ]);
            }
        }

        return $data;
    }

    protected function mutateBeforeUpdate(array $data, array $existing): array
    {
        $roleId = $data['role_id'] ?? $existing['role_id'];
        $permissionId = $data['permission_id'] ?? $existing['permission_id'];

        $exists = DB::table($this->table)
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->where('id', '!=', $existing['id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'role_permission' => ['Role and permission pair already exists.'],
            ]);
        }

        return $data;
    }
}
