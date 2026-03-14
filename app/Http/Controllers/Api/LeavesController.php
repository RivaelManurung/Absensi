<?php

namespace App\Http\Controllers\Api;

class LeavesController extends BaseCrudController
{
    protected string $table = 'leaves';

    protected array $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected bool $usesCreatedAt = true;

    protected function storeRules(): array
    {
        return [
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'leave_type_id' => ['required', 'uuid', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:20'],
            'approved_by' => ['nullable', 'uuid', 'exists:users,id'],
            'approved_at' => ['nullable', 'date'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'employee_id' => ['sometimes', 'uuid', 'exists:employees,id'],
            'leave_type_id' => ['sometimes', 'uuid', 'exists:leave_types,id'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'reason' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:20'],
            'approved_by' => ['nullable', 'uuid', 'exists:users,id'],
            'approved_at' => ['nullable', 'date'],
        ];
    }
}
