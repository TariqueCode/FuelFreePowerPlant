<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Production may already contain this table from an earlier schema.
        // Reconcile the missing menu discriminator before the default-menu seeder runs.
        if (!Schema::hasTable('navigation_menu_items')) {
            return;
        }

        if (!Schema::hasColumn('navigation_menu_items', 'menu')) {
            Schema::table('navigation_menu_items', function (Blueprint $table) {
                $table->string('menu', 60)->default('main')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('navigation_menu_items') && Schema::hasColumn('navigation_menu_items', 'menu')) {
            Schema::table('navigation_menu_items', function (Blueprint $table) {
                $table->dropColumn('menu');
            });
        }
    }
};
