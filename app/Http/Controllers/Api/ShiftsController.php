<?php

namespace App\Http\Controllers\Api;

class ShiftsController extends BaseCrudController
{
    protected string $table = 'shifts';

    protected array $fillable = [
        'name',
        'start_time',
        'end_time',
        'late_tolerance_minutes',
    ];

    protected bool $usesCreatedAt = true;

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:50'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i'],
            'late_tolerance_minutes' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
