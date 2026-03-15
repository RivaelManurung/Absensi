<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $this->syncLegacyRolesToSpatie((string) $user->id);

        return response()->json([
            'data' => $this->serializeUser($user->fresh()),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, false)) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is invalid.'],
            ]);
        }

        $request->session()->regenerate();

        /** @var User|null $user */
        $user = Auth::user();

        if (! $user?->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact administrator.'],
            ]);
        }

        $this->syncLegacyRolesToSpatie((string) $user->id);

        return response()->json([
            'message' => 'Login successful.',
            'data' => $this->serializeUser($user->fresh()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    private function syncLegacyRolesToSpatie(string $userId): void
    {
        $user = Auth::user();

        if (! ($user instanceof User) || $user->getRoleNames()->isNotEmpty()) {
            return;
        }

        $legacyRoleNames = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->pluck('roles.name')
            ->filter(fn ($role) => is_string($role) && $role !== '')
            ->values()
            ->all();

        if (! empty($legacyRoleNames)) {
            $user->syncRoles($legacyRoleNames);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeUser(User $user): array
    {
        return [
            'id' => (string) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'is_active' => (bool) $user->is_active,
            'roles' => $user->getRoleNames()->values()->all(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
        ];
    }
}
