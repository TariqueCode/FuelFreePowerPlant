<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->foreignId('navigation_parent_id')->nullable()->after('navigation_order')->constrained('site_content_items')->nullOnDelete();
            $table->index(['navigation_parent_id', 'navigation_order']);
        });
    }

    public function down(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->dropForeign(['navigation_parent_id']);
            $table->dropIndex(['navigation_parent_id', 'navigation_order']);
            $table->dropColumn('navigation_parent_id');
        });
    }
};
