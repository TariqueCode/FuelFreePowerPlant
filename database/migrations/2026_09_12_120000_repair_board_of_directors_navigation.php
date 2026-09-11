<?php

use App\Models\ManagementProfileFolder;
use App\Models\NavigationMenuItem;
use App\Models\SiteContentItem;
use App\Models\HomepageSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('management_profile_folders') || ! Schema::hasTable('navigation_menu_items')) {
            return;
        }

        $folder = ManagementProfileFolder::query()
            ->where('status', 'published')
            ->orderBy('sort_order')->orderBy('id')
            ->first();

        if (! $folder) return;

        // Repair legacy Management navigation entries in-place so existing menu
        // placement is preserved while the destination becomes the real folder.
        NavigationMenuItem::query()
            ->where('menu', 'main')
            ->where(function ($query) {
                $query->where('route_name', 'management')
                    ->orWhere('source_key', 'like', 'management_folder:%');
            })
            ->get()
            ->each(function (NavigationMenuItem $item) use ($folder): void {
                $item->label = $folder->name;
                $item->label_override = null;
                $item->url = '/'.ltrim($folder->slug, '/');
                $item->route_name = null;
                $item->source_type = 'folder';
                $item->source_key = 'management_folder:'.$folder->id;
                $item->permission_key = null;
                $item->saveQuietly();
            });

        $canonicalKey = 'management_folder:'.$folder->id;
        $hasCanonical = NavigationMenuItem::query()
            ->where('menu', 'main')
            ->where('source_key', $canonicalKey)
            ->exists();

        if (! $hasCanonical) {
            $maxOrder = (int) (NavigationMenuItem::query()->where('menu', 'main')->whereNull('parent_id')->max('sort_order') ?? -1);
            NavigationMenuItem::query()->create([
                'menu' => 'main',
                'group' => null,
                'parent_id' => null,
                'label' => $folder->name,
                'label_override' => null,
                'url' => '/'.ltrim($folder->slug, '/'),
                'route_name' => null,
                'target' => '_self',
                'icon' => null,
                'is_visible' => true,
                'sort_order' => $maxOrder + 1,
                'source_key' => $canonicalKey,
                'source_type' => 'folder',
                'area' => 'public',
                'permission_key' => null,
            ]);
        }

        // The homepage Board of Directors section should use the canonical folder
        // and its published profiles. Do not overwrite a deliberate selection.
        if (Schema::hasTable('homepage_sections')) {
            $section = HomepageSection::query()->where('key', 'management')->first();
            if ($section) {
                $settings = is_array($section->settings) ? $section->settings : [];
                $settings['folder_id'] = $folder->id;
                $settings['mode'] = 'selected';
                $existingIds = array_values(array_unique(array_filter(array_map('intval', (array) ($settings['ids'] ?? [])))));
                $publishedIds = SiteContentItem::query()
                    ->where('type', 'management')
                    ->where('management_profile_folder_id', $folder->id)
                    ->where('status', 'published')
                    ->orderBy('sort_order')->orderBy('id')
                    ->pluck('id')->map(fn ($id) => (int) $id)->all();
                $selected = array_values(array_intersect($existingIds, $publishedIds));
                $settings['ids'] = $selected ?: array_slice($publishedIds, 0, 4);
                $section->settings = $settings;
                if ($section->is_enabled === null || ! $section->is_enabled) {
                    $section->is_enabled = true;
                }
                $section->saveQuietly();
            }
        }
    }

    public function down(): void
    {
        // Data-repair migration: intentionally non-destructive on rollback.
    }
};
