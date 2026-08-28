<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesk_replies', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 30);
            $table->unsignedBigInteger('source_id');
            $table->foreignId('admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('to_email', 190);
            $table->string('subject', 255);
            $table->longText('body');
            $table->timestamp('sent_at')->nullable();
            $table->string('status', 20)->default('sent')->index();
            $table->text('error')->nullable();
            $table->timestamps();
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_replies');
    }
};