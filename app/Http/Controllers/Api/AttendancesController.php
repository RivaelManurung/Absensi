<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;

class AttendancesController extends BaseCrudController
{
    protected string $table = 'attendances';

    protected array $fillable = [
        'employee_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'check_in_method',
        'check_out_method',
        'status',
    ];

    protected bool $usesCreatedAt = true;
    protected bool $usesUpdatedAt = true;

    protected function storeRules(): array
    {
        return [
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'attendance_date' => ['required', 'date'],
            'check_in_time' => ['nullable', 'date'],
            'check_out_time' => ['nullable', 'date'],
            'check_in_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'check_in_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'check_out_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'check_out_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'check_in_method' => ['nullable', 'string', 'max:20'],
            'check_out_method' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'employee_id' => ['sometimes', 'uuid', 'exists:employees,id'],
            'attendance_date' => ['sometimes', 'date'],
            'check_in_time' => ['nullable', 'date'],
            'check_out_time' => ['nullable', 'date'],
            'check_in_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'check_in_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'check_out_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'check_out_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'check_in_method' => ['nullable', 'string', 'max:20'],
            'check_out_method' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function mutateBeforeStore(array $data): array
    {
        if (isset($data['employee_id'], $data['attendance_date'])) {
            $exists = \Illuminate\Support\Facades\DB::table($this->table)
                ->where('employee_id', $data['employee_id'])
                ->where('attendance_date', $data['attendance_date'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'attendance' => ['Attendance for this employee and date already exists.'],
                ]);
            }
        }

        return $data;
    }

    protected function mutateBeforeUpdate(array $data, array $existing): array
    {
        $employeeId = $data['employee_id'] ?? $existing['employee_id'];
        $attendanceDate = $data['attendance_date'] ?? $existing['attendance_date'];

        $exists = \Illuminate\Support\Facades\DB::table($this->table)
            ->where('employee_id', $employeeId)
            ->where('attendance_date', $attendanceDate)
            ->where('id', '!=', $existing['id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'attendance' => ['Attendance for this employee and date already exists.'],
            ]);
        }

        return $data;
    }
}
