<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
            'Cache' => $this->cacheCheck(),
            'Queue' => $this->queueCheck(),
            'PHP upload limits' => ['status' => $this->phpUploadLimitOk(), 'detail' => 'upload_max_filesize='.ini_get('upload_max_filesize').', post_max_size='.ini_get('post_max_size')],
        ];

        return view('admin.health.index', compact('checks'));
    }

    private function databaseCheck(): array
    {
        try { DB::select('select 1'); return ['status' => true, 'detail' => 'Connection healthy']; }
        catch (\Throwable $e) { return ['status' => false, 'detail' => 'Database connection failed']; }
    }

    private function cacheCheck(): array { try { $key='admin.health.probe'; Cache::put($key,'ok',10); $ok=Cache::get($key)==='ok'; Cache::forget($key); return ['status'=>$ok,'detail'=>$ok?'Read/write healthy':'Read/write failed']; } catch (\Throwable $e) { Log::warning('Health cache check failed',['exception'=>$e]); return ['status'=>false,'detail'=>'Cache check failed']; } }

    private function queueCheck(): array { return ['status'=>config('queue.default') !== null,'detail'=>'Driver: '.(string) config('queue.default')]; }

    private function phpUploadLimitOk(): bool { $toBytes=function($v){$v=trim((string)$v); $n=(float)$v; return $n*(match(strtoupper(substr($v,-1))){'G'=>1073741824,'M'=>1048576,'K'=>1024,default=>1});}; return $toBytes(ini_get('upload_max_filesize')) >= 50*1048576 && $toBytes(ini_get('post_max_size')) >= 50*1048576; }

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
