<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles', 'employee'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function find(string $id): ?User
    {
        return User::query()->with(['roles', 'employee'])->find($id);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): User
    {
        return User::query()->create($payload);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function update(User $user, array $payload): User
    {
        $user->fill($payload);
        $user->save();

        return $user;
    }

    public function activeCount(): int
    {
        return User::query()->where('is_active', true)->count();
    }
}
