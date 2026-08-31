<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_pages')) return;

        Schema::table('cms_pages', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_pages', 'use_global_framework')) {
                $table->boolean('use_global_framework')->default(true);
            }
            if (! Schema::hasColumn('cms_pages', 'use_global_header')) {
                $table->boolean('use_global_header')->default(true);
            }
            if (! Schema::hasColumn('cms_pages', 'use_global_footer')) {
                $table->boolean('use_global_footer')->default(true);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cms_pages')) return;

        Schema::table('cms_pages', function (Blueprint $table) {
            $drop = [];
            foreach (['use_global_framework','use_global_header','use_global_footer'] as $column) {
                if (Schema::hasColumn('cms_pages', $column)) $drop[] = $column;
            }
            if ($drop) $table->dropColumn($drop);
        });
    }
};