<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\Rule;

class RolesController extends BaseCrudController
{
    protected string $table = 'roles';

    protected array $fillable = [
        'name',
        'description',
    ];

    protected bool $usesCreatedAt = true;

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:roles,name'],
            'description' => ['nullable', 'string'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($id, 'id')],
            'description' => ['nullable', 'string'],
        ];
    }
}
