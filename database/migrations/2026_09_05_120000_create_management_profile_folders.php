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
        Schema::create('management_profile_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status', 20)->default('published');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('site_content_items', function (Blueprint $table) {
            $table->foreignId('management_profile_folder_id')
                ->nullable()
                ->after('type')
                ->constrained('management_profile_folders')
                ->nullOnDelete();
            $table->index(['type', 'management_profile_folder_id', 'status', 'sort_order']);
        });

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

    public function down(): void
    {
        Schema::table('site_content_items', function (Blueprint $table) {
            $table->dropForeign(['management_profile_folder_id']);
            $table->dropIndex(['type', 'management_profile_folder_id', 'status', 'sort_order']);
            $table->dropColumn('management_profile_folder_id');
        });

        Schema::dropIfExists('management_profile_folders');
    }
};
