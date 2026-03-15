<?php

namespace App\Services;

use App\Models\Employee;
use App\Repositories\BarcodeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Milon\Barcode\DNS2D;

class BarcodeService
{
    public function __construct(
        private readonly BarcodeRepository $barcodes,
        private readonly ActivityLogService $activityLogs
    ) {
    }

    public function list(int $perPage = 10)
    {
        return $this->barcodes->paginate($perPage);
    }

    public function employees()
    {
        return Employee::query()
            ->with('user')
            ->where('status', 'active')
            ->orderBy('employee_code')
            ->get();
    }

    public function detail(string $id)
    {
        return $this->barcodes->find($id);
    }

    public function qrPngBase64(string $value): string
    {
        $barcode = new DNS2D();

        return $barcode->getBarcodePNG($value, 'QRCODE', 8, 8);
    }

    public function qrPngBinary(string $value): string
    {
        return (string) base64_decode($this->qrPngBase64($value), true);
    }

    public function generateForEmployee(string $employeeId, Request $request)
    {
        return DB::transaction(function () use ($employeeId, $request) {
            $active = $this->barcodes->activeByEmployee($employeeId);
            if ($active) {
                return $active;
            }

            $employee = Employee::query()->with('user')->findOrFail($employeeId);
            $code = strtoupper('ABS-'.$employee->employee_code.'-'.Str::random(8));

            $barcode = $this->barcodes->create([
                'employee_id' => $employeeId,
                'code' => $code,
                'type' => 'qr',
                'is_active' => true,
                'generated_at' => now(),
                'generated_by' => $request->user()?->id,
            ]);

            $this->activityLogs->log(
                module: 'barcode',
                action: 'generate',
                description: 'Generate barcode for '.$employee->employee_code,
                userId: (string) $request->user()?->id,
                request: $request,
                meta: ['barcode_id' => $barcode->id, 'employee_id' => $employeeId],
            );

            return $barcode;
        });
    }

    public function deactivate(string $barcodeId, Request $request): void
    {
        DB::transaction(function () use ($barcodeId, $request): void {
            $barcode = $this->barcodes->find($barcodeId);

            if (! $barcode) {
                return;
            }

            $this->barcodes->deactivate($barcode);

            $this->activityLogs->log(
                module: 'barcode',
                action: 'deactivate',
                description: 'Deactivate barcode '.$barcode->code,
                userId: (string) $request->user()?->id,
                request: $request,
                meta: ['barcode_id' => $barcode->id],
            );
        });
    }

    public function regenerate(string $barcodeId, Request $request)
    {
        return DB::transaction(function () use ($barcodeId, $request) {
            $current = $this->barcodes->find($barcodeId);
            if (! $current) {
                return null;
            }

            $this->barcodes->deactivate($current);

            return $this->generateForEmployee($current->employee_id, $request);
        });
    }
}
