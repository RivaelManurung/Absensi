<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = DB::table('attendances')
            ->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id')
            ->leftJoin('users', 'users.id', '=', 'employees.user_id')
            ->select([
                'attendances.id',
                'attendances.employee_id',
                'attendances.attendance_date',
                'attendances.check_in_time',
                'attendances.check_out_time',
                'attendances.check_in_lat',
                'attendances.check_in_lng',
                'attendances.check_out_lat',
                'attendances.check_out_lng',
                'attendances.check_in_method',
                'attendances.check_out_method',
                'attendances.status',
                'attendances.created_at',
                'attendances.updated_at',
                'employees.employee_code',
                'users.name as employee_name',
            ]);

        $search = trim($request->string('query')->toString());
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('users.name', 'like', "%{$search}%")
                    ->orWhere('attendances.status', 'like', "%{$search}%")
                    ->orWhere('employees.employee_code', 'like', "%{$search}%");
            });
        }

        $date = trim($request->string('date')->toString());
        if ($date !== '') {
            $query->whereDate('attendances.attendance_date', $date);
        }

        $status = trim($request->string('status')->toString());
        if ($status !== '' && $status !== 'all') {
            $query->where('attendances.status', $status);
        }

        $employeeId = trim($request->string('employee_id')->toString());
        if ($employeeId !== '') {
            $query->where('attendances.employee_id', $employeeId);
        }

        $paginator = $query
            ->orderByDesc('attendances.attendance_date')
            ->paginate($perPage);

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn ($row) => (array) $row)
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

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
