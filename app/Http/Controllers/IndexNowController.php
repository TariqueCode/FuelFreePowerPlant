<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class IndexNowController extends Controller
{
    public function key(string $key): Response
    {
        $configuredKey = trim((string) config('fuelfree.seo.indexnow_key', ''));

        abort_unless($configuredKey !== '' && hash_equals($configuredKey, $key), 404);

        return response($configuredKey, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
