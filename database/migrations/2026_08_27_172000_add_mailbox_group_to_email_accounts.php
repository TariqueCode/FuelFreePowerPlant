<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_accounts', function (Blueprint $table) {
            $table->string('mailbox_group', 40)->default('general')->after('display_name')->index();
        });
    }

    public function down(): void
    {
        Schema::table('email_accounts', fn (Blueprint $table) => $table->dropColumn('mailbox_group'));
    }
};