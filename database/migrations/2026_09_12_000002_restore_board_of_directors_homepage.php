<?php

use App\Models\HomepageSection;
use App\Models\ManagementProfileFolder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $section = HomepageSection::query()->where('key', 'management')->first();
        $folder = ManagementProfileFolder::query()
            ->where('status', 'published')
            ->with(['profiles' => fn ($query) => $query->where('status', 'published')->orderBy('sort_order')->orderBy('title')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $section || ! $folder) {
            return;
        }

        $settings = is_array($section->settings) ? $section->settings : [];
        $settings['folder_id'] = $folder->id;
        $settings['mode'] = 'selected';
        $settings['ids'] = $folder->profiles->take(4)->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $settings['limit'] = max(1, min(100, (int) ($settings['limit'] ?? 4)));
        $settings['layout'] = in_array(($settings['layout'] ?? 'left'), ['left', 'center', 'right'], true) ? $settings['layout'] : 'left';

        $section->settings = $settings;
        $section->is_enabled = true;
        $section->save();
    }

    public function down(): void
    {
        // Do not disable a homepage section during rollback: the previous state
        // is user-managed and cannot be reconstructed safely from migration data.
    }
};
