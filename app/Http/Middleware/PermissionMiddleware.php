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

        $allowed = $user && collect($permissions)->contains(
            fn (string $permission) => $user->hasPermission($permission)
        );

        abort_unless($allowed, 403);

        return $next($request);
    }
}
