<?php

namespace App\Http\Controllers\Api;

class PositionsController extends BaseCrudController
{
    protected string $table = 'positions';

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
