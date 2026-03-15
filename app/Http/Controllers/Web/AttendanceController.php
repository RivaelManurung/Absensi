<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {
    }

    public function index(): View
    {
        $rows = $this->attendanceService
            ->list(100)
            ->items();

        $rows = collect($rows)
            ->map(fn ($item) => [
                (string) $item->attendance_date?->format('Y-m-d'),
                $item->employee?->employee_code ?? '-',
                $item->check_in_time ? $item->check_in_time->format('H:i') : '-',
                $item->check_out_time ? $item->check_out_time->format('H:i') : '-',
                ucfirst((string) ($item->status ?? '-')),
            ])
            ->all();

        return view('attendance.index', ['rows' => $rows]);
    }

    public function create(): View
    {
        $employees = $this->attendanceService->employeesForForm();

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

        $this->attendanceService->create([
            'employee_id' => $data['employee_id'],
            'attendance_date' => $data['attendance_date'],
            'check_in_time' => $data['check_in_time'] ?? null,
            'check_out_time' => $data['check_out_time'] ?? null,
            'status' => strtolower($data['status']),
        ], $request);

        return redirect()->route('attendance.index')->with('success', 'Attendance created successfully.');
    }

    public function report(Request $request): View
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $summary = DB::table('attendances')
            ->when($startDate, fn ($query) => $query->whereDate('attendance_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('attendance_date', '<=', $endDate))
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
            ->when($startDate, fn ($query) => $query->whereDate('attendances.attendance_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('attendances.attendance_date', '<=', $endDate))
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
            'startDate' => $startDate,
            'endDate' => $endDate,
            'rows' => $rows,
        ]);
    }
}
