<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\Rule;

class PermissionsController extends BaseCrudController
{
    protected string $table = 'permissions';

    protected array $fillable = [
        'name',
        'description',
    ];

    protected bool $usesCreatedAt = true;

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:permissions,name'],
            'description' => ['nullable', 'string'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('permissions', 'name')->ignore($id, 'id')],
            'description' => ['nullable', 'string'],
        ];
    }
}
