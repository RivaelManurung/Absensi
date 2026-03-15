<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogs
    ) {
    }

    public function index(): View
    {
        return view('activity-logs.index', [
            'logs' => $this->activityLogs->list(15),
        ]);
    }

    public function show(string $id): View
    {
        $log = $this->activityLogs->detail($id);
        abort_if($log === null, 404);

        return view('activity-logs.show', [
            'log' => $log,
        ]);
    }
}
