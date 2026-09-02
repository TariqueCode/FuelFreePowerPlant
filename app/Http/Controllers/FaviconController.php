<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FaviconController
{
    public function __invoke(): Response
    {
        $logoPath = SystemSetting::query()
            ->where('key', 'company.logo_path')
            ->value('value');

        if ($logoPath) {
            $logoPath = ltrim((string) $logoPath, '/');

            if (Storage::disk('public')->exists($logoPath)) {
                $absolutePath = Storage::disk('public')->path($logoPath);
                $mime = Storage::disk('public')->mimeType($logoPath) ?: 'image/png';

                return response()->file($absolutePath, [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=3600',
                ]);
            }
        }

        $fallback = public_path('favicon.svg');

        return response()->file($fallback, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
