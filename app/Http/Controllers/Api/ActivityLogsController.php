<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = DB::table('activity_logs')
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->select([
                'activity_logs.id',
                'activity_logs.user_id',
                'activity_logs.action',
                'activity_logs.module',
                'activity_logs.description',
                'activity_logs.meta',
                'activity_logs.ip_address',
                'activity_logs.user_agent',
                'activity_logs.created_at',
                'users.name as user_name',
                'users.email as user_email',
            ]);

        $search = trim($request->string('query')->toString());
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('activity_logs.description', 'like', "%{$search}%")
                    ->orWhere('activity_logs.action', 'like', "%{$search}%")
                    ->orWhere('activity_logs.module', 'like', "%{$search}%");
            });
        }

        $module = trim($request->string('module')->toString());
        if ($module !== '') {
            $query->where('activity_logs.module', 'like', "%{$module}%");
        }

        $action = trim($request->string('action')->toString());
        if ($action !== '') {
            $query->where('activity_logs.action', 'like', "%{$action}%");
        }

        $date = trim($request->string('date')->toString());
        if ($date !== '') {
            $query->whereDate('activity_logs.created_at', $date);
        }

        $paginator = $query
            ->orderByDesc('activity_logs.created_at')
            ->paginate($perPage);

        return response()->json([
            'data' => collect($paginator->items())
                ->map(function ($row): array {
                    $record = (array) $row;

                    if (isset($record['meta']) && is_string($record['meta']) && $record['meta'] !== '') {
                        $decoded = json_decode($record['meta'], true);
                        $record['meta'] = is_array($decoded) ? $decoded : null;
                    }

                    return $record;
                })
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $row = DB::table('activity_logs')
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->where('activity_logs.id', $id)
            ->select([
                'activity_logs.id',
                'activity_logs.user_id',
                'activity_logs.action',
                'activity_logs.module',
                'activity_logs.description',
                'activity_logs.meta',
                'activity_logs.ip_address',
                'activity_logs.user_agent',
                'activity_logs.created_at',
                'users.name as user_name',
                'users.email as user_email',
            ])
            ->first();

        if (! $row) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        $record = (array) $row;

        if (isset($record['meta']) && is_string($record['meta']) && $record['meta'] !== '') {
            $decoded = json_decode($record['meta'], true);
            $record['meta'] = is_array($decoded) ? $decoded : null;
        }

        return response()->json([
            'data' => $record,
        ]);
    }
}
