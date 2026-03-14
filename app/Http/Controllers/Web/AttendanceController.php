<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $rows = DB::table('attendances')
            ->join('employees', 'employees.id', '=', 'attendances.employee_id')
            ->orderByDesc('attendances.attendance_date')
            ->orderByDesc('attendances.created_at')
            ->limit(100)
            ->get([
                'attendances.attendance_date',
                'employees.employee_code',
                'attendances.check_in_time',
                'attendances.check_out_time',
                'attendances.status',
            ])
            ->map(function ($row) {
                return [
                    $row->attendance_date,
                    $row->employee_code,
                    $row->check_in_time ? now()->parse($row->check_in_time)->format('H:i') : '-',
                    $row->check_out_time ? now()->parse($row->check_out_time)->format('H:i') : '-',
                    ucfirst((string) ($row->status ?? '-')),
                ];
            })
            ->all();

        return view('attendance.index', ['rows' => $rows]);
    }

    public function create(): View
    {
        $employees = DB::table('employees')
            ->orderBy('employee_code')
            ->get(['id', 'employee_code']);

        return view('attendance.create', ['employees' => $employees]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'attendance_date' => ['required', 'date'],
            'check_in_time' => ['nullable', 'date'],
            'check_out_time' => ['nullable', 'date', 'after_or_equal:check_in_time'],
            'status' => ['required', 'string', 'max:20'],
        ]);

        $exists = DB::table('attendances')
            ->where('employee_id', $data['employee_id'])
            ->whereDate('attendance_date', $data['attendance_date'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'attendance_date' => 'Attendance already exists for this employee and date.',
            ])->withInput();
        }

        DB::table('attendances')->insert([
            'id' => (string) Str::uuid(),
            'employee_id' => $data['employee_id'],
            'attendance_date' => $data['attendance_date'],
            'check_in_time' => $data['check_in_time'] ?? null,
            'check_out_time' => $data['check_out_time'] ?? null,
            'status' => strtolower($data['status']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('attendance.index')->with('success', 'Attendance created successfully.');
    }

    public function report(): View
    {
        $summary = DB::table('attendances')
            ->selectRaw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count")
            ->selectRaw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count")
            ->selectRaw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->first();

        $total = max(1, (int) (($summary->present_count ?? 0) + ($summary->late_count ?? 0) + ($summary->absent_count ?? 0)));

        $presentRate = (int) round((($summary->present_count ?? 0) / $total) * 100);
        $lateRate = (int) round((($summary->late_count ?? 0) / $total) * 100);
        $absenceRate = (int) round((($summary->absent_count ?? 0) / $total) * 100);

        $rows = DB::table('attendances')
            ->join('employees', 'employees.id', '=', 'attendances.employee_id')
            ->join('departments', 'departments.id', '=', 'employees.department_id')
            ->select(
                'departments.name as department_name',
                DB::raw("SUM(CASE WHEN attendances.status = 'present' THEN 1 ELSE 0 END) as present_count"),
                DB::raw("SUM(CASE WHEN attendances.status = 'late' THEN 1 ELSE 0 END) as late_count"),
                DB::raw("SUM(CASE WHEN attendances.status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            )
            ->groupBy('departments.name')
            ->orderBy('departments.name')
            ->get()
            ->map(fn ($row) => [
                $row->department_name,
                (string) $row->present_count,
                (string) $row->late_count,
                (string) $row->absent_count,
            ])
            ->all();

        return view('attendance.report', [
            'presentRate' => $presentRate,
            'lateRate' => $lateRate,
            'absenceRate' => $absenceRate,
            'overtimeCount' => 0,
            'rows' => $rows,
        ]);
    }
}
