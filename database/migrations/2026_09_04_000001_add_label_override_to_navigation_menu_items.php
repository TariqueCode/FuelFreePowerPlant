<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('navigation_menu_items', 'label_override')) {
            Schema::table('navigation_menu_items', function (Blueprint $table): void {
                $table->string('label_override', 160)->nullable()->after('label');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('navigation_menu_items', 'label_override')) {
            Schema::table('navigation_menu_items', function (Blueprint $table): void {
                $table->dropColumn('label_override');
            });
        }
    }
};
