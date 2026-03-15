<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BarcodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarcodesController extends Controller
{
    public function __construct(
        private readonly BarcodeService $barcodes
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = DB::table('barcodes')
            ->leftJoin('employees', 'employees.id', '=', 'barcodes.employee_id')
            ->leftJoin('users as employee_users', 'employee_users.id', '=', 'employees.user_id')
            ->leftJoin('users as generators', 'generators.id', '=', 'barcodes.generated_by')
            ->select([
                'barcodes.id',
                'barcodes.employee_id',
                'barcodes.code',
                'barcodes.type',
                'barcodes.is_active',
                'barcodes.generated_at',
                'barcodes.generated_by',
                'barcodes.created_at',
                'barcodes.updated_at',
                'employees.employee_code',
                'employee_users.name as employee_name',
                'generators.name as generated_by_name',
            ]);

        $search = trim($request->string('query')->toString());
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('barcodes.code', 'like', "%{$search}%")
                    ->orWhere('employee_users.name', 'like', "%{$search}%")
                    ->orWhere('employees.employee_code', 'like', "%{$search}%");
            });
        }

        $status = strtolower(trim($request->string('status')->toString()));
        if ($status === 'active') {
            $query->where('barcodes.is_active', true);
        }

        if ($status === 'inactive') {
            $query->where('barcodes.is_active', false);
        }

        $employeeId = trim($request->string('employee_id')->toString());
        if ($employeeId !== '') {
            $query->where('barcodes.employee_id', $employeeId);
        }

        $paginator = $query
            ->orderByDesc('barcodes.generated_at')
            ->paginate($perPage);

        return response()->json([
            'data' => collect($paginator->items())->map(fn ($row) => (array) $row)->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
        ]);

        $barcode = $this->barcodes->generateForEmployee($data['employee_id'], $request);

        return response()->json([
            'message' => 'Created successfully.',
            'data' => $this->appendQrImage((array) $barcode),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $barcode = DB::table('barcodes')
            ->leftJoin('employees', 'employees.id', '=', 'barcodes.employee_id')
            ->leftJoin('users as employee_users', 'employee_users.id', '=', 'employees.user_id')
            ->leftJoin('users as generators', 'generators.id', '=', 'barcodes.generated_by')
            ->where('barcodes.id', $id)
            ->select([
                'barcodes.id',
                'barcodes.employee_id',
                'barcodes.code',
                'barcodes.type',
                'barcodes.is_active',
                'barcodes.generated_at',
                'barcodes.generated_by',
                'barcodes.created_at',
                'barcodes.updated_at',
                'employees.employee_code',
                'employee_users.name as employee_name',
                'generators.name as generated_by_name',
            ])
            ->first();

        if (! $barcode) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        return response()->json([
            'data' => $this->appendQrImage((array) $barcode),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = DB::table('barcodes')->where('id', $id)->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        return response()->json([
            'message' => 'Deleted successfully.',
        ]);
    }

    public function regenerate(Request $request, string $id): JsonResponse
    {
        $barcode = $this->barcodes->regenerate($id, $request);

        if (! $barcode) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        return response()->json([
            'message' => 'Barcode regenerated successfully.',
            'data' => $this->appendQrImage((array) $barcode),
        ]);
    }

    public function deactivate(Request $request, string $id): JsonResponse
    {
        $existing = DB::table('barcodes')->where('id', $id)->exists();

        if (! $existing) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        $this->barcodes->deactivate($id, $request);

        return response()->json([
            'message' => 'Barcode deactivated successfully.',
        ]);
    }

    /**
     * @param array<string, mixed> $record
     * @return array<string, mixed>
     */
    private function appendQrImage(array $record): array
    {
        $code = isset($record['code']) ? (string) $record['code'] : '';

        if ($code !== '') {
            $record['qr_image_data_url'] = 'data:image/png;base64,'.$this->barcodes->qrPngBase64($code);
        }

        return $record;
    }
}
