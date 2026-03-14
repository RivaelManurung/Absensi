<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $attendanceToday = DB::table('attendances')
            ->whereDate('attendance_date', $today)
            ->count();

        $onLeave = DB::table('leaves')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('status', 'approved')
            ->count();

        $lateCount = DB::table('attendances')
            ->whereDate('attendance_date', $today)
            ->where('status', 'late')
            ->count();

        $rows = DB::table('attendances')
            ->join('employees', 'employees.id', '=', 'attendances.employee_id')
            ->join('users', 'users.id', '=', 'employees.user_id')
            ->whereDate('attendances.attendance_date', $today)
            ->orderByDesc('attendances.check_in_time')
            ->limit(10)
            ->get([
                'employees.employee_code',
                'users.name',
                'attendances.check_in_time',
                'attendances.status',
            ])
            ->map(function ($row) {
                return [
                    $row->employee_code,
                    $row->name,
                    $row->check_in_time ? now()->parse($row->check_in_time)->format('H:i') : '-',
                    ucfirst((string) ($row->status ?? '-')),
                ];
            })
            ->all();

        return view('dashboard.index', [
            'attendanceToday' => $attendanceToday,
            'onLeave' => $onLeave,
            'lateCount' => $lateCount,
            'rows' => $rows,
        ]);
    }
}
