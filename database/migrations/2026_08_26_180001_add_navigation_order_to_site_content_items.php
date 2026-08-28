<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->integer('navigation_order')->nullable()->after('show_in_navigation')->index();
        });

        $items = DB::table('site_content_items')
            ->where('type', 'company')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get(['id']);

        foreach ($items as $index => $item) {
            DB::table('site_content_items')->where('id', $item->id)->update([
                'navigation_order' => $index + 1,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->dropIndex(['navigation_order']);
            $table->dropColumn('navigation_order');
        });
    }
};
