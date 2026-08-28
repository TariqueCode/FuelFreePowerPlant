<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_desk_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_account_id')->nullable()->constrained('email_accounts')->nullOnDelete();
            $table->foreignId('inquiry_id')->nullable()->constrained('inquiries')->cascadeOnDelete();
            $table->foreignId('career_application_id')->nullable()->constrained('career_applications')->cascadeOnDelete();
            $table->string('from_address', 255);
            $table->string('to_address', 255);
            $table->string('subject', 255);
            $table->longText('body');
            $table->string('status', 30)->default('sent')->index();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['inquiry_id', 'created_at']);
            $table->index(['career_application_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_desk_replies');
    }
};
