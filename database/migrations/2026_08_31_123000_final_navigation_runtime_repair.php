<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Last-mile production repair for navigation databases that were created
     * by an older migration and later missed one or more navigation columns.
     *
     * This migration is intentionally idempotent and non-destructive.
     */
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        $columns = [
            'menu' => fn (Blueprint $table) => $table->string('menu', 60)->default('main'),
            'group' => fn (Blueprint $table) => $table->string('group', 100)->nullable(),
            'parent_id' => fn (Blueprint $table) => $table->unsignedBigInteger('parent_id')->nullable(),
            'label' => fn (Blueprint $table) => $table->string('label', 160)->default(''),
            'url' => fn (Blueprint $table) => $table->string('url', 500)->nullable(),
            'route_name' => fn (Blueprint $table) => $table->string('route_name', 160)->nullable(),
            'target' => fn (Blueprint $table) => $table->string('target', 20)->default('_self'),
            'icon' => fn (Blueprint $table) => $table->string('icon', 100)->nullable(),
            'is_visible' => fn (Blueprint $table) => $table->boolean('is_visible')->default(true),
            'sort_order' => fn (Blueprint $table) => $table->unsignedInteger('sort_order')->default(0),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ];

        foreach ($columns as $name => $definition) {
            if (! Schema::hasColumn('navigation_menu_items', $name)) {
                Schema::table('navigation_menu_items', $definition);
            }
        }
    }

    public function down(): void
    {
        // Non-destructive by design; these fields are required by the
        // production navigation builder.
    }
};
