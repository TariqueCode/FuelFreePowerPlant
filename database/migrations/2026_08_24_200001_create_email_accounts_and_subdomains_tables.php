<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('address', 255)->unique();
            $table->string('display_name', 150)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->string('imap_host', 255)->nullable();
            $table->unsignedSmallInteger('imap_port')->default(993);
            $table->string('smtp_host', 255)->nullable();
            $table->unsignedSmallInteger('smtp_port')->default(465);
            $table->string('username', 255)->nullable();
            $table->text('password')->nullable();
            $table->timestamps();
        });

        Schema::create('subdomains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 180)->unique();
            $table->string('target', 500)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->boolean('ssl_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subdomains');
        Schema::dropIfExists('email_accounts');
    }
};
