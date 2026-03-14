<?php

namespace App\Http\Controllers\Api;

class DepartmentsController extends BaseCrudController
{
    protected string $table = 'departments';

    protected array $fillable = [
        'name',
    ];

    protected bool $usesCreatedAt = true;

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
        ];
    }
}
