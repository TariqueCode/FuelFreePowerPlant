<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // Retired Site Content mutations must behave as removed endpoints,
        // regardless of the caller's legacy website permissions.
        if ($request->isMethod('PATCH') && $request->is('admin/site-content/news/*/toggle')) {
            abort(404);
        }

        $user = $request->user();

        // Super Admin is the platform authority and must not be blocked by a
        // stale or partially-synced permission_role pivot in production.
        $allowed = $user && ($user->hasRole('super-admin') || collect($permissions)->contains(
            fn (string $permission) => $user->hasPermission($permission)
        ));

        abort_unless($allowed, 403);

        return $next($request);
    }
}
