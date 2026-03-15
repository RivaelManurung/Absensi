<?php

namespace App\Repositories;

use App\Models\Barcode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BarcodeRepository
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Barcode::query()
            ->with(['employee.user', 'generator'])
            ->latest('generated_at')
            ->paginate($perPage);
    }

    public function find(string $id): ?Barcode
    {
        return Barcode::query()->with(['employee.user', 'generator'])->find($id);
    }

    public function activeByEmployee(string $employeeId): ?Barcode
    {
        return Barcode::query()
            ->where('employee_id', $employeeId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): Barcode
    {
        return Barcode::query()->create($payload);
    }

    public function deactivate(Barcode $barcode): Barcode
    {
        $barcode->is_active = false;
        $barcode->save();

        return $barcode;
    }
}
