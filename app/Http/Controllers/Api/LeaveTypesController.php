<?php

namespace App\Http\Controllers\Api;

class LeaveTypesController extends BaseCrudController
{
    protected string $table = 'leave_types';

    protected array $fillable = [
        'name',
        'max_days',
    ];

    protected bool $usesCreatedAt = true;

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'max_days' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:50'],
            'max_days' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
