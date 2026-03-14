<?php

namespace App\Http\Controllers\Api;

class AttendanceLogsController extends BaseCrudController
{
    protected string $table = 'attendance_logs';

    protected array $fillable = [
        'attendance_id',
        'type',
        'latitude',
        'longitude',
        'device_info',
    ];

    protected bool $usesCreatedAt = true;

    protected function storeRules(): array
    {
        return [
            'attendance_id' => ['required', 'uuid', 'exists:attendances,id'],
            'type' => ['required', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'device_info' => ['nullable', 'string'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'attendance_id' => ['sometimes', 'uuid', 'exists:attendances,id'],
            'type' => ['sometimes', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'device_info' => ['nullable', 'string'],
        ];
    }
}
