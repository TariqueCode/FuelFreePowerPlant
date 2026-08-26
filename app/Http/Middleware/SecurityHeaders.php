<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
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

        // The logo configured in Admin > Settings is also the universal favicon.
        if ($response->headers->get('Content-Type') && str_contains(strtolower($response->headers->get('Content-Type')), 'text/html')) {
            $logoPath = SystemSetting::query()->where('key', 'company.logo_path')->value('value');
            if ($logoPath) {
                $faviconUrl = asset('storage/' . ltrim($logoPath, '/'));
                $content = $response->getContent();
                if (is_string($content) && stripos($content, '</head>') !== false && stripos($content, 'rel="icon"') === false && stripos($content, "rel='icon'") === false) {
                    $favicon = '<link rel="icon" href="' . e($faviconUrl) . '"><link rel="apple-touch-icon" href="' . e($faviconUrl) . '">';
                    $content = preg_replace('/<\/head>/i', $favicon . '</head>', $content, 1);
                    $response->setContent($content);
                }
            }
        }

        return $response;
    }
}
