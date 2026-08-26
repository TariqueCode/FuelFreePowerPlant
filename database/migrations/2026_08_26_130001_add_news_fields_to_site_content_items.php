<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->string('cover_alt', 255)->nullable()->after('image_path');
            $table->boolean('is_featured')->default(false)->index()->after('published_at');
            $table->string('meta_title', 255)->nullable()->after('is_featured');
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->dropColumn(['cover_alt', 'is_featured', 'meta_title', 'meta_description']);
        });
    }
};
