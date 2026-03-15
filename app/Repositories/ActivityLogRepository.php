<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogRepository
{
    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): ActivityLog
    {
        return ActivityLog::query()->create($payload);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ActivityLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function find(string $id): ?ActivityLog
    {
        return ActivityLog::query()->with('user')->find($id);
    }
}
