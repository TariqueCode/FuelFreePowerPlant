<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->string('designation', 255)->nullable()->after('excerpt');
            $table->string('phone', 50)->nullable()->after('designation');
            $table->string('email', 255)->nullable()->after('phone');
            $table->string('visiting_card_path', 500)->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->dropColumn(['designation', 'phone', 'email', 'visiting_card_path']);
        });
    }
};
