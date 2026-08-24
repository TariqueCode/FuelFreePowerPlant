<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        $allowed = $user && collect($permissions)->contains(
            fn (string $permission) => $user->hasPermission($permission)
        );

        abort_unless($allowed, 403);

        return $next($request);
    }
}
