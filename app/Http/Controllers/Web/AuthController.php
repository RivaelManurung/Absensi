<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
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

        if (! Auth::user()?->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact administrator.'],
            ]);
        }

        $this->syncLegacyRolesToSpatie((string) Auth::id());

        return redirect()->route('dashboard.index')->with('success', 'Login successful.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have logged out.');
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
}
