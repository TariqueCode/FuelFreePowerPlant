<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('helpdesk_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_account_id')->constrained('email_accounts')->cascadeOnDelete();
            $table->string('mailbox_group',20)->index();
            $table->unsignedBigInteger('external_uid')->nullable();
            $table->string('message_id',1000)->nullable();
            $table->string('fingerprint',64)->unique();
            $table->string('sender_name',255)->nullable();
            $table->string('sender_email',320);
            $table->text('to_email')->nullable();
            $table->text('cc_email')->nullable();
            $table->string('subject',1000)->nullable();
            $table->longText('body_html')->nullable();
            $table->longText('body_text')->nullable();
            $table->string('status',30)->default('new')->index();
            $table->timestamp('received_at')->nullable()->index();
            $table->timestamp('imported_at')->nullable();
            $table->timestamp('external_deleted_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        Schema::create('helpdesk_email_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('helpdesk_email_id')->constrained('helpdesk_emails')->cascadeOnDelete();
            $table->string('part',100);
            $table->string('filename',500);
            $table->string('mime_type',255)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('path',1000);
            $table->timestamps();
            $table->index('helpdesk_email_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_email_attachments');
        Schema::dropIfExists('helpdesk_emails');
    }
};
