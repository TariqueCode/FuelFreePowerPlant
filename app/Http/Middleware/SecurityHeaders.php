<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
        }

        // The logo configured in Admin > Settings is the universal favicon for every HTML page,
        // including the public website, authentication pages and admin dashboard.
        if ($response->headers->get('Content-Type') && str_contains(strtolower($response->headers->get('Content-Type')), 'text/html')) {
            $logoPath = config('fuelfree.company.logo_path');
            $content = $response->getContent();

            if ($logoPath && is_string($content) && stripos($content, '</head>') !== false) {
                $faviconUrl = asset('storage/' . ltrim($logoPath, '/'));

                // Remove any stale/default icon declarations so the configured logo is authoritative.
                $content = preg_replace('/<link\b[^>]*\brel=["\'](?:shortcut icon|icon|apple-touch-icon)["\'][^>]*>\s*/i', '', $content);
                $favicon = '<link rel="icon" type="image/png" href="' . e($faviconUrl) . '"><link rel="apple-touch-icon" href="' . e($faviconUrl) . '">';
                $content = preg_replace('/<\/head>/i', $favicon . '</head>', $content, 1);
                $response->setContent($content);
            }
        }

        return $response;
    }
}
