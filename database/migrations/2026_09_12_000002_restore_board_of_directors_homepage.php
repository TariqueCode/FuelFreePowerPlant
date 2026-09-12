<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_sections') || ! Schema::hasTable('management_profile_folders') || ! Schema::hasTable('site_content_items')) {
            return;
        }

        $section = DB::table('homepage_sections')->where('key', 'management')->first();
        $folder = DB::table('management_profile_folders')
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $section || ! $folder) {
            return;
        }

        $profiles = DB::table('site_content_items')
            ->where('type', 'management')
            ->where('management_profile_folder_id', $folder->id)
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->limit(4)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $settings = [];
        if (isset($section->settings) && is_string($section->settings) && trim($section->settings) !== '') {
            $decoded = json_decode($section->settings, true);
            if (is_array($decoded)) {
                $settings = $decoded;
            }
        } elseif (isset($section->settings) && is_array($section->settings)) {
            $settings = $section->settings;
        }

        $settings['folder_id'] = (int) $folder->id;
        $settings['mode'] = 'selected';
        $settings['ids'] = $profiles;
        $settings['limit'] = max(1, min(100, (int) ($settings['limit'] ?? 4)));

        $layout = $settings['layout'] ?? 'left';
        $settings['layout'] = in_array($layout, ['left', 'center', 'right'], true) ? $layout : 'left';

        DB::table('homepage_sections')->where('id', $section->id)->update([
            'settings' => json_encode($settings, JSON_UNESCAPED_SLASHES),
            'is_enabled' => true,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Do not disable a homepage section during rollback: the previous state
        // is user-managed and cannot be reconstructed safely from migration data.
    }
};
