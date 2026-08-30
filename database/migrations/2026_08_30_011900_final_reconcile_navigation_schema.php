<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        $definitions = [
            'menu' => fn (Blueprint $table) => $table->string('menu', 60)->default('main')->index(),
            'group' => fn (Blueprint $table) => $table->string('group', 100)->nullable()->index(),
            'parent_id' => fn (Blueprint $table) => $table->unsignedBigInteger('parent_id')->nullable()->index(),
            'label' => fn (Blueprint $table) => $table->string('label', 160)->default(''),
            'url' => fn (Blueprint $table) => $table->string('url', 500)->nullable(),
            'route_name' => fn (Blueprint $table) => $table->string('route_name', 160)->nullable(),
            'target' => fn (Blueprint $table) => $table->string('target', 20)->default('_self'),
            'icon' => fn (Blueprint $table) => $table->string('icon', 100)->nullable(),
            'is_visible' => fn (Blueprint $table) => $table->boolean('is_visible')->default(true)->index(),
            'sort_order' => fn (Blueprint $table) => $table->unsignedInteger('sort_order')->default(0)->index(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ];

        foreach ($definitions as $column => $definition) {
            if (! Schema::hasColumn('navigation_menu_items', $column)) {
                Schema::table('navigation_menu_items', $definition);
            }
        }

        if (Schema::hasColumn('navigation_menu_items', 'parent_id')) {
            try {
                $foreignExists = collect(Schema::getForeignKeys('navigation_menu_items'))
                    ->contains(fn (array $foreign) =>
                        in_array('parent_id', $foreign['columns'] ?? [], true)
                    );

                if (! $foreignExists) {
                    Schema::table('navigation_menu_items', function (Blueprint $table) {
                        $table->foreign('parent_id')
                            ->references('id')
                            ->on('navigation_menu_items')
                            ->nullOnDelete();
                    });
                }
            } catch (\Throwable $e) {
                // Keep legacy production databases usable even when FK
                // metadata or constraint changes are unavailable.
            }
        }
    }

    public function down(): void
    {
        // Non-destructive for existing production installations.
    }
};
