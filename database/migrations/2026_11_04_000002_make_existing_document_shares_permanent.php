<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('documents')
            || ! Schema::hasColumn('documents', 'share_enabled')
            || ! Schema::hasColumn('documents', 'share_expires_at')) {
            return;
        }

        // Preserve all currently active share tokens and remove their expiry.
        DB::table('documents')
            ->where('share_enabled', true)
            ->whereNotNull('share_token')
            ->update(['share_expires_at' => null]);
    }

    public function down(): void
    {
        // Expiration timestamps cannot be reconstructed safely.
    }
};
