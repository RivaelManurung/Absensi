<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = DB::table('employees')
            ->leftJoin('users', 'users.id', '=', 'employees.user_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->leftJoin('positions', 'positions.id', '=', 'employees.position_id')
            ->select([
                'employees.id',
                'employees.user_id',
                'employees.employee_code',
                'employees.department_id',
                'employees.position_id',
                'employees.phone',
                'employees.hire_date',
                'employees.status',
                'employees.created_at',
                'employees.updated_at',
                'users.name as user_name',
                'users.email as user_email',
                'departments.name as department_name',
                'positions.name as position_name',
            ]);

        $search = trim($request->string('query')->toString());
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('employees.employee_code', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('departments.name', 'like', "%{$search}%")
                    ->orWhere('positions.name', 'like', "%{$search}%");
            });
        }

        $status = trim($request->string('status')->toString());
        if ($status !== '' && $status !== 'all') {
            $query->where('employees.status', $status);
        }

        $departmentId = trim($request->string('department_id')->toString());
        if ($departmentId !== '') {
            $query->where('employees.department_id', $departmentId);
        }

        $positionId = trim($request->string('position_id')->toString());
        if ($positionId !== '') {
            $query->where('employees.position_id', $positionId);
        }

        $paginator = $query
            ->orderByDesc('employees.created_at')
            ->paginate($perPage);

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn ($row) => [
                    'id' => $row->id,
                    'user_id' => $row->user_id,
                    'employee_code' => $row->employee_code,
                    'department_id' => $row->department_id,
                    'position_id' => $row->position_id,
                    'phone' => $row->phone,
                    'hire_date' => $row->hire_date,
                    'status' => $row->status,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'user_name' => $row->user_name,
                    'user_email' => $row->user_email,
                    'department_name' => $row->department_name,
                    'position_name' => $row->position_name,
                ])
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
