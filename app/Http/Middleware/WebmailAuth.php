<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebmailAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('webmail.email') || !$request->session()->has('webmail.password')) {
            return redirect()->route('webmail.login');
        }
        return $next($request);
    }
}
