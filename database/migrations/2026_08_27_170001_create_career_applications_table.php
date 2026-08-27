<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 190);
            $table->string('phone', 40)->nullable();
            $table->string('position', 180)->nullable();
            $table->string('education', 255)->nullable();
            $table->string('experience', 180)->nullable();
            $table->string('location', 180)->nullable();
            $table->text('message')->nullable();
            $table->string('cv_path', 500);
            $table->string('cv_original_name', 255)->nullable();
            $table->string('status', 30)->default('new')->index();
            $table->boolean('consent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_applications');
    }
};