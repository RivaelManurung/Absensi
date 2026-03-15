<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly ActivityLogService $activityLogs
    ) {
    }

    public function list(int $perPage = 10)
    {
        return $this->users->paginate($perPage);
    }

    public function roles()
    {
        return Role::query()->orderBy('name')->get();
    }

    public function detail(string $id): ?User
    {
        return $this->users->find($id);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function update(User $user, array $payload, Request $request): User
    {
        return DB::transaction(function () use ($user, $payload, $request): User {
            $updateData = [
                'name' => $payload['name'],
                'email' => $payload['email'],
                'is_active' => (bool) ($payload['is_active'] ?? false),
            ];

            if (! empty($payload['password'])) {
                $updateData['password'] = Hash::make((string) $payload['password']);
            }

            $updated = $this->users->update($user, $updateData);

            $roleNames = array_values(array_filter((array) ($payload['roles'] ?? [])));
            $updated->syncRoles($roleNames);

            $this->activityLogs->log(
                module: 'user',
                action: 'update',
                description: 'User '.$updated->email.' updated.',
                userId: (string) $request->user()?->id,
                request: $request,
                meta: ['target_user_id' => $updated->id, 'roles' => $roleNames],
            );

            return $updated;
        });
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload, Request $request): User
    {
        return DB::transaction(function () use ($payload, $request): User {
            $user = $this->users->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => Hash::make((string) $payload['password']),
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]);

            $roleNames = array_values(array_filter((array) ($payload['roles'] ?? [])));
            if (! empty($roleNames)) {
                $user->syncRoles($roleNames);
            }

            $this->activityLogs->log(
                module: 'user',
                action: 'create',
                description: 'User '.$user->email.' created.',
                userId: (string) $request->user()?->id,
                request: $request,
                meta: ['target_user_id' => $user->id, 'roles' => $roleNames],
            );

            return $user;
        });
    }
}
