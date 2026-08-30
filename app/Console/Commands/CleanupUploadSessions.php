<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupUploadSessions extends Command
{
    protected $signature = 'fuel-free:cleanup-upload-sessions {--hours=2 : Remove sessions older than this many hours}';

    protected $description = 'Remove abandoned large-file upload sessions and their temporary buffers.';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $cutoff = now()->subHours($hours)->timestamp;
        $disk = Storage::disk('local');
        $removed = 0;

        foreach ($disk->directories('private') as $userDirectory) {
            $uploadsDirectory = $userDirectory . '/.uploads';
            if (! $disk->exists($uploadsDirectory)) {
                continue;
            }

            foreach ($disk->files($uploadsDirectory) as $file) {
                if ($disk->lastModified($file) >= $cutoff) {
                    continue;
                }

                $disk->delete($file);
                $removed++;
            }
        }

        $this->info("Removed {$removed} abandoned upload file(s).");

        return self::SUCCESS;
    }
}
