<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserRolesController extends BaseCrudController
{
    protected string $table = 'user_roles';

    protected array $fillable = [
        'user_id',
        'role_id',
    ];

    protected function storeRules(): array
    {
        return [
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'role_id' => ['required', 'uuid', 'exists:roles,id'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'user_id' => ['sometimes', 'uuid', 'exists:users,id'],
            'role_id' => ['sometimes', 'uuid', 'exists:roles,id'],
        ];
    }

    protected function mutateBeforeStore(array $data): array
    {
        if (isset($data['user_id'], $data['role_id'])) {
            $exists = DB::table($this->table)
                ->where('user_id', $data['user_id'])
                ->where('role_id', $data['role_id'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'user_role' => ['User and role pair already exists.'],
                ]);
            }
        }

        return $data;
    }

    protected function mutateBeforeUpdate(array $data, array $existing): array
    {
        $userId = $data['user_id'] ?? $existing['user_id'];
        $roleId = $data['role_id'] ?? $existing['role_id'];

        $exists = DB::table($this->table)
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->where('id', '!=', $existing['id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'user_role' => ['User and role pair already exists.'],
            ]);
        }

        return $data;
    }
}
