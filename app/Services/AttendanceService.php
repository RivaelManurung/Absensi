<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        private readonly AttendanceRepository $attendances,
        private readonly ActivityLogService $activityLogs
    ) {
    }

    public function list(int $perPage = 10)
    {
        return $this->attendances->paginate($perPage);
    }

    public function report(?string $startDate, ?string $endDate, int $perPage = 15)
    {
        return $this->attendances->filterByDateRange($startDate, $endDate, $perPage);
    }

    public function employeesForForm()
    {
        return Employee::query()
            ->with('user')
            ->where('status', 'active')
            ->orderBy('employee_code')
            ->get();
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload, Request $request): void
    {
        DB::transaction(function () use ($payload, $request): void {
            $attendance = $this->attendances->create($payload);

            $this->activityLogs->log(
                module: 'attendance',
                action: 'create',
                description: 'Attendance created for employee ID '.$attendance->employee_id,
                userId: (string) $request->user()?->id,
                request: $request,
                meta: [
                    'attendance_id' => $attendance->id,
                    'attendance_date' => (string) $attendance->attendance_date,
                    'status' => $attendance->status,
                ],
            );
        });
    }
}
