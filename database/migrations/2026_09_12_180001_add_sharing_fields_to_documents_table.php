<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'share_token')) {
                $table->string('share_token', 64)->nullable()->unique();
            }
            if (!Schema::hasColumn('documents', 'share_enabled')) {
                $table->boolean('share_enabled')->default(false)->index();
            }
            if (!Schema::hasColumn('documents', 'share_expires_at')) {
                $table->timestamp('share_expires_at')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'share_expires_at')) {
                $table->dropColumn('share_expires_at');
            }
            if (Schema::hasColumn('documents', 'share_enabled')) {
                $table->dropColumn('share_enabled');
            }
            if (Schema::hasColumn('documents', 'share_token')) {
                $table->dropUnique(['share_token']);
                $table->dropColumn('share_token');
            }
        });
    }
};
