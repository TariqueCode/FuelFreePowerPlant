<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_pages')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_pages', 'meta_title')) {
                $table->string('meta_title', 255)->nullable();
            }
            if (! Schema::hasColumn('cms_pages', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }
            if (! Schema::hasColumn('cms_pages', 'builder_blocks')) {
                $table->json('builder_blocks')->nullable();
            }
            if (! Schema::hasColumn('cms_pages', 'template')) {
                $table->string('template', 80)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cms_pages')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table) {
            $drop = [];
            foreach (['meta_title', 'meta_description', 'builder_blocks', 'template'] as $column) {
                if (Schema::hasColumn('cms_pages', $column)) {
                    $drop[] = $column;
                }
            }
            if ($drop) {
                $table->dropColumn($drop);
            }
        });
    }
};
