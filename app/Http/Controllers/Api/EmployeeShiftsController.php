<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;

class EmployeeShiftsController extends BaseCrudController
{
    protected string $table = 'employee_shifts';

    protected array $fillable = [
        'employee_id',
        'shift_id',
        'effective_date',
    ];

    protected function storeRules(): array
    {
        return [
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'shift_id' => ['required', 'uuid', 'exists:shifts,id'],
            'effective_date' => ['required', 'date'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'employee_id' => ['sometimes', 'uuid', 'exists:employees,id'],
            'shift_id' => ['sometimes', 'uuid', 'exists:shifts,id'],
            'effective_date' => ['sometimes', 'date'],
        ];
    }

    protected function mutateBeforeStore(array $data): array
    {
        if (isset($data['employee_id'], $data['shift_id'], $data['effective_date'])) {
            $exists = \Illuminate\Support\Facades\DB::table($this->table)
                ->where('employee_id', $data['employee_id'])
                ->where('shift_id', $data['shift_id'])
                ->where('effective_date', $data['effective_date'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'employee_shift' => ['Employee shift for this effective date already exists.'],
                ]);
            }
        }

        return $data;
    }

    protected function mutateBeforeUpdate(array $data, array $existing): array
    {
        $employeeId = $data['employee_id'] ?? $existing['employee_id'];
        $shiftId = $data['shift_id'] ?? $existing['shift_id'];
        $effectiveDate = $data['effective_date'] ?? $existing['effective_date'];

        $exists = \Illuminate\Support\Facades\DB::table($this->table)
            ->where('employee_id', $employeeId)
            ->where('shift_id', $shiftId)
            ->where('effective_date', $effectiveDate)
            ->where('id', '!=', $existing['id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'employee_shift' => ['Employee shift for this effective date already exists.'],
            ]);
        }

        return $data;
    }
}
