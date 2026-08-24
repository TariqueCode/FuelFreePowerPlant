<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HealthController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(request()->user()->hasPermission('health.view'), 403);

        $checks = [
            'Application' => ['status' => true, 'detail' => app()->environment()],
            'Database' => $this->databaseCheck(),
            'Private storage' => $this->storageCheck(),
            'Debug mode' => ['status' => !config('app.debug'), 'detail' => config('app.debug') ? 'APP_DEBUG is enabled' : 'Disabled'],
            'Encryption key' => ['status' => (bool) config('app.key'), 'detail' => config('app.key') ? 'Configured' : 'Missing'],
        ];

        return view('admin.health.index', compact('checks'));
    }

    private function databaseCheck(): array
    {
        try { DB::select('select 1'); return ['status' => true, 'detail' => 'Connection healthy']; }
        catch (\Throwable $e) { return ['status' => false, 'detail' => 'Database connection failed']; }
    }

    private function storageCheck(): array
    {
        try {
            $disk = Storage::disk('local');
            $probe = 'private/.health-check';
            $disk->put($probe, 'ok');
            $disk->delete($probe);
            return ['status' => true, 'detail' => 'Writable'];
        } catch (\Throwable $e) { return ['status' => false, 'detail' => 'Storage is not writable']; }
    }
}
