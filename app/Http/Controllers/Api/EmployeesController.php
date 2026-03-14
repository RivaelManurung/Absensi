<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\Rule;

class EmployeesController extends BaseCrudController
{
    protected string $table = 'employees';

    protected array $fillable = [
        'user_id',
        'employee_code',
        'department_id',
        'position_id',
        'phone',
        'hire_date',
        'status',
    ];

    protected bool $usesCreatedAt = true;
    protected bool $usesUpdatedAt = true;

    protected function storeRules(): array
    {
        return [
            'user_id' => ['required', 'uuid', 'exists:users,id', 'unique:employees,user_id'],
            'employee_code' => ['required', 'string', 'max:20', 'unique:employees,employee_code'],
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'position_id' => ['required', 'uuid', 'exists:positions,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'user_id' => ['sometimes', 'uuid', 'exists:users,id', Rule::unique('employees', 'user_id')->ignore($id, 'id')],
            'employee_code' => ['sometimes', 'string', 'max:20', Rule::unique('employees', 'employee_code')->ignore($id, 'id')],
            'department_id' => ['sometimes', 'uuid', 'exists:departments,id'],
            'position_id' => ['sometimes', 'uuid', 'exists:positions,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:20'],
        ];
    }
}
