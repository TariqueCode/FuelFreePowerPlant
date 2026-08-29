<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_layout_settings', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 40)->default('site')->index();
            $table->string('key', 120);
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['scope', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_layout_settings');
    }
};
