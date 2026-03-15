<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends BaseCrudController
{
    protected string $table = 'users';

    protected array $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected array $hidden = ['password'];

    protected bool $usesCreatedAt = true;
    protected bool $usesUpdatedAt = true;

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $query = DB::table('users')
            ->leftJoin('user_roles', 'user_roles.user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'user_roles.role_id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.is_active',
                'users.created_at',
                'users.updated_at',
                DB::raw('MIN(roles.name) as role_name'),
            ])
            ->groupBy([
                'users.id',
                'users.name',
                'users.email',
                'users.is_active',
                'users.created_at',
                'users.updated_at',
            ]);

        $search = trim($request->string('query')->toString());
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('roles.name', 'like', "%{$search}%");
            });
        }

        $role = trim($request->string('role')->toString());
        if ($role !== '' && $role !== 'all') {
            $query->where('roles.name', $role);
        }

        $status = strtolower(trim($request->string('status')->toString()));
        if ($status === 'active') {
            $query->where('users.is_active', true);
        }

        if ($status === 'inactive') {
            $query->where('users.is_active', false);
        }

        $paginator = $query
            ->orderByDesc('users.created_at')
            ->paginate($perPage);

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn ($row) => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'email' => $row->email,
                    'is_active' => (bool) $row->is_active,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'role_name' => $row->role_name,
                ])
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    protected function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function updateRules(string $id): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:100', Rule::unique('users', 'email')->ignore($id, 'id')],
            'password' => ['sometimes', 'string', 'min:6', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function mutateBeforeStore(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_active'] = $data['is_active'] ?? true;

        return $data;
    }

    protected function mutateBeforeUpdate(array $data, array $existing): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    public function resetPassword(Request $request, string $id): JsonResponse
    {
        $existing = DB::table($this->table)->where('id', $id)->first();

        if (! $existing) {
            return response()->json(['message' => 'Data not found.'], 404);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:255'],
        ]);

        DB::table($this->table)
            ->where('id', $id)
            ->update([
                'password' => Hash::make($data['password']),
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }
}
