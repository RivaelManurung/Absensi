<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class BaseCrudController extends Controller
{
    protected string $table;

    /**
     * @var list<string>
     */
    protected array $fillable = [];

    /**
     * @var list<string>
     */
    protected array $hidden = [];

    protected bool $usesCreatedAt = false;
    protected bool $usesUpdatedAt = false;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = DB::table($this->table);
        $orderBy = $this->resolveOrderBy($request->string('order_by')->toString());
        $orderDirection = strtolower($request->string('order_direction', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';

        $paginator = $query->orderBy($orderBy, $orderDirection)->paginate($perPage);

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn ($item) => $this->sanitizeRecord((array) $item))
                ->values(),
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
        $validator = Validator::make($request->all(), $this->storeRules());

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = Arr::only($validator->validated(), $this->fillable);

        if (! array_key_exists('id', $data)) {
            $data['id'] = (string) Str::uuid();
        }

        if ($this->usesCreatedAt) {
            $data['created_at'] = now();
        }

        if ($this->usesUpdatedAt) {
            $data['updated_at'] = now();
        }

        $data = $this->mutateBeforeStore($data);
        DB::table($this->table)->insert($data);

        return response()->json([
            'message' => 'Created successfully.',
            'data' => $this->sanitizeRecord($data),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $record = DB::table($this->table)->where('id', $id)->first();

        if (! $record) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        return response()->json([
            'data' => $this->sanitizeRecord((array) $record),
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $existing = DB::table($this->table)->where('id', $id)->first();

        if (! $existing) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        $validator = Validator::make($request->all(), $this->updateRules($id));

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = Arr::only($validator->validated(), $this->fillable);

        if ($this->usesUpdatedAt) {
            $data['updated_at'] = now();
        }

        $data = $this->mutateBeforeUpdate($data, (array) $existing);

        if (! empty($data)) {
            DB::table($this->table)->where('id', $id)->update($data);
        }

        $updated = DB::table($this->table)->where('id', $id)->first();

        return response()->json([
            'message' => 'Updated successfully.',
            'data' => $this->sanitizeRecord((array) $updated),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = DB::table($this->table)->where('id', $id)->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        return response()->json(['message' => 'Deleted successfully.']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function mutateBeforeStore(array $data): array
    {
        return $data;
    }

    /**
     * @param array<string, mixed> $existing
     * @return array<string, mixed>
     */
    protected function mutateBeforeUpdate(array $data, array $existing): array
    {
        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    protected function sanitizeRecord(array $record): array
    {
        foreach ($this->hidden as $hiddenColumn) {
            unset($record[$hiddenColumn]);
        }

        return $record;
    }

    protected function resolveOrderBy(string $requested): string
    {
        if ($requested !== '' && in_array($requested, $this->fillable, true)) {
            return $requested;
        }

        if ($this->usesCreatedAt) {
            return 'created_at';
        }

        return 'id';
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function storeRules(): array;

    /**
     * @return array<string, mixed>
     */
    abstract protected function updateRules(string $id): array;
}
