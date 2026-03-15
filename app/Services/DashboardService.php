<?php

namespace App\Services;

use App\Repositories\AttendanceRepository;
use App\Repositories\DashboardRepository;

class DashboardService
{
    public function __construct(
        private readonly AttendanceRepository $attendances,
        private readonly DashboardRepository $dashboard
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $today = now()->toDateString();

        $attendanceToday = $this->attendances->countToday($today);
        $lateCount = $this->attendances->countLateToday($today);
        $onLeave = $this->dashboard->countOnLeave($today);

        $rows = $this->attendances
            ->latestByDate($today)
            ->map(function ($attendance): array {
                return [
                    $attendance->employee?->employee_code ?? '-',
                    $attendance->employee?->user?->name ?? '-',
                    $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : '-',
                    ucfirst((string) ($attendance->status ?? '-')),
                ];
            })
            ->all();

        return [
            'attendanceToday' => $attendanceToday,
            'lateCount' => $lateCount,
            'onLeave' => $onLeave,
            'rows' => $rows,
        ];
    }
}
