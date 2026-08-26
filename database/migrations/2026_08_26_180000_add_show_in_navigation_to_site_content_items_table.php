<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('site_content_items', 'show_in_navigation')) {
            Schema::table('site_content_items', function (Blueprint $table) {
                $table->boolean('show_in_navigation')->default(false)->index();
            });
        }

        DB::table('site_content_items')
            ->where('type', 'company')
            ->where('status', 'published')
            ->update(['show_in_navigation' => true]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('site_content_items', 'show_in_navigation')) {
            Schema::table('site_content_items', function (Blueprint $table) {
                $table->dropColumn('show_in_navigation');
            });
        }
    }
};
