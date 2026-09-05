<?php

use App\Models\ManagementProfileFolder;
use App\Models\SiteContentItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('management_profile_folders')) {
            Schema::create('management_profile_folders', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('status', 20)->default('published');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('site_content_items', 'management_profile_folder_id')) {
            Schema::table('site_content_items', function (Blueprint $table) {
                $table->foreignId('management_profile_folder_id')
                    ->nullable()
                    ->after('type')
                    ->constrained('management_profile_folders')
                    ->nullOnDelete();
                $table->index(
                    ['type', 'management_profile_folder_id', 'status', 'sort_order'],
                    'sci_management_profile_folder_idx'
                );
            });
        }

        if (! ManagementProfileFolder::query()->exists()) {
            $folder = ManagementProfileFolder::query()->create([
                'name' => 'Board of Directors',
                'slug' => Str::slug('Board of Directors'),
                'status' => 'published',
                'sort_order' => 1,
            ]);

            SiteContentItem::query()
                ->where('type', 'management')
                ->whereNull('management_profile_folder_id')
                ->update(['management_profile_folder_id' => $folder->id]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('site_content_items', 'management_profile_folder_id')) {
            Schema::table('site_content_items', function (Blueprint $table) {
                $table->dropForeign(['management_profile_folder_id']);
                $table->dropIndex('sci_management_profile_folder_idx');
                $table->dropColumn('management_profile_folder_id');
            });
        }

        Schema::dropIfExists('management_profile_folders');
    }
};
