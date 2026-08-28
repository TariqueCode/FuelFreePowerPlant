<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('email_accounts')) {
            return;
        }

        Schema::table('email_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('email_accounts', 'provisioned')) {
                $table->boolean('provisioned')->default(false)->after('status');
            }

            if (! Schema::hasColumn('email_accounts', 'provider_message')) {
                $table->text('provider_message')->nullable()->after('provisioned');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('email_accounts')) {
            return;
        }

        Schema::table('email_accounts', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('email_accounts', 'provider_message')) {
                $columns[] = 'provider_message';
            }

            if (Schema::hasColumn('email_accounts', 'provisioned')) {
                $columns[] = 'provisioned';
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
