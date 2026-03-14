<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (empty($roles)) {
            return $next($request);
        }

        $allowed = collect($roles)
            ->map(fn (string $role) => strtolower(trim($role)))
            ->filter()
            ->values();

        $userRoles = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $user->id)
            ->pluck('roles.name')
            ->map(fn (string $role) => strtolower($role));

        if ($allowed->intersect($userRoles)->isEmpty()) {
            abort(403, 'Unauthorized role.');
        }

        return $next($request);
    }
}
