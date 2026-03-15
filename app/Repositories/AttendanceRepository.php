<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AttendanceRepository
{
    public function countToday(string $date): int
    {
        return Attendance::query()
            ->whereDate('attendance_date', $date)
            ->count();
    }

    public function countLateToday(string $date): int
    {
        return Attendance::query()
            ->whereDate('attendance_date', $date)
            ->where('status', 'late')
            ->count();
    }

    public function latestByDate(string $date, int $limit = 10): Collection
    {
        return Attendance::query()
            ->with(['employee.user'])
            ->whereDate('attendance_date', $date)
            ->orderByDesc('check_in_time')
            ->limit($limit)
            ->get();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Attendance::query()
            ->with(['employee.user'])
            ->latest('attendance_date')
            ->paginate($perPage);
    }

    public function filterByDateRange(?string $startDate, ?string $endDate, int $perPage = 15): LengthAwarePaginator
    {
        return Attendance::query()
            ->with(['employee.user'])
            ->when($startDate, fn ($query) => $query->whereDate('attendance_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('attendance_date', '<=', $endDate))
            ->orderByDesc('attendance_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): Attendance
    {
        return Attendance::query()->create($payload);
    }
}
