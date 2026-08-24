<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('document_folders')->nullOnDelete();
            $table->string('name', 150);
            $table->timestamps();
            $table->unique(['user_id', 'parent_id', 'name']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('document_folders')->nullOnDelete();
            $table->string('original_name', 255);
            $table->string('stored_name', 255);
            $table->string('disk', 50)->default('local');
            $table->string('path', 500);
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('extension', 20)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'folder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_folders');
    }
};
