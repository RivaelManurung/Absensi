<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends BaseCrudController
{
    protected string $table = 'users';

    protected array $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected array $hidden = ['password'];

    protected bool $usesCreatedAt = true;
    protected bool $usesUpdatedAt = true;

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:100', Rule::unique('users', 'email')->ignore($id, 'id')],
            'password' => ['sometimes', 'string', 'min:6', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function mutateBeforeStore(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_active'] = $data['is_active'] ?? true;

        return $data;
    }

    protected function mutateBeforeUpdate(array $data, array $existing): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }
}
