<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $rows = DB::table('employees')
            ->join('users', 'users.id', '=', 'employees.user_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->leftJoin('positions', 'positions.id', '=', 'employees.position_id')
            ->orderBy('employees.employee_code')
            ->get([
                'employees.id',
                'employees.employee_code',
                'users.name',
                'departments.name as department_name',
                'positions.name as position_name',
                'employees.status',
            ])
            ->map(fn ($row) => [
                'id' => $row->id,
                'employee_code' => $row->employee_code,
                'name' => $row->name,
                'department_name' => $row->department_name ?? '-',
                'position_name' => $row->position_name ?? '-',
                'status' => ucfirst((string) ($row->status ?? '-')),
            ])
            ->all();

        return view('employee.index', ['rows' => $rows]);
    }

    public function create(): View
    {
        return view('employee.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,id', 'unique:employees,user_id'],
            'employee_code' => ['required', 'string', 'max:20', 'unique:employees,employee_code'],
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'position_id' => ['required', 'uuid', 'exists:positions,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        DB::table('employees')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $data['user_id'],
            'employee_code' => $data['employee_code'],
            'department_id' => $data['department_id'],
            'position_id' => $data['position_id'],
            'phone' => $data['phone'] ?? null,
            'hire_date' => $data['hire_date'] ?? null,
            'status' => strtolower($data['status']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('employee.index')->with('success', 'Employee created successfully.');
    }

    public function edit(string $id): View
    {
        $employee = DB::table('employees')->where('id', $id)->first();

        abort_if($employee === null, 404);

        return view('employee.edit', array_merge($this->formData(), [
            'employee' => $employee,
        ]));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $employee = DB::table('employees')->where('id', $id)->first();
        abort_if($employee === null, 404);

        $data = $request->validate([
            'user_id' => [
                'required',
                'uuid',
                'exists:users,id',
                Rule::unique('employees', 'user_id')->ignore($id, 'id'),
            ],
            'employee_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'employee_code')->ignore($id, 'id'),
            ],
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'position_id' => ['required', 'uuid', 'exists:positions,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        DB::table('employees')
            ->where('id', $id)
            ->update([
                'user_id' => $data['user_id'],
                'employee_code' => $data['employee_code'],
                'department_id' => $data['department_id'],
                'position_id' => $data['position_id'],
                'phone' => $data['phone'] ?? null,
                'hire_date' => $data['hire_date'] ?? null,
                'status' => strtolower($data['status']),
                'updated_at' => now(),
            ]);

        return redirect()->route('employee.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'users' => DB::table('users')->orderBy('name')->get(['id', 'name']),
            'departments' => DB::table('departments')->orderBy('name')->get(['id', 'name']),
            'positions' => DB::table('positions')->orderBy('name')->get(['id', 'name']),
        ];
    }
}
