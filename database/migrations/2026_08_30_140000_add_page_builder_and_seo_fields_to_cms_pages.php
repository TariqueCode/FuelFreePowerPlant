<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->string('meta_title', 255)->nullable()->after('is_published');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->json('builder_blocks')->nullable()->after('meta_description');
            $table->string('template', 80)->nullable()->after('builder_blocks');
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'builder_blocks', 'template']);
        });
    }
};
