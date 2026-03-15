<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function __construct(
        private readonly ActivityLogRepository $activityLogs
    ) {
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function log(string $module, string $action, ?string $description, ?string $userId, Request $request, array $meta = []): void
    {
        $this->activityLogs->create([
            'user_id' => $userId,
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'meta' => empty($meta) ? null : $meta,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function list(int $perPage = 15)
    {
        return $this->activityLogs->paginate($perPage);
    }

    public function detail(string $id)
    {
        return $this->activityLogs->find($id);
    }
}
