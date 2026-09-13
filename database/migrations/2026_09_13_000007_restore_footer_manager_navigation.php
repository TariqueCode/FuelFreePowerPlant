<?php

use App\Models\NavigationMenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (! class_exists(NavigationMenuItem::class)) return;

        $website = NavigationMenuItem::query()
            ->where('menu', 'dashboard')->where('area', 'dashboard')->whereNull('parent_id')
            ->where('source_type', 'folder')->where('label', 'Website')->first();
        if (! $website) return;

        $profile = NavigationMenuItem::query()
            ->where('menu', 'dashboard')->where('area', 'dashboard')
            ->where('source_key', 'route:admin.management.index')->first();
        if ($profile) {
            $profile->update([
                'label' => 'Profile Builder', 'url' => '/admin/profile-builder',
                'route_name' => 'admin.profile-builder.index', 'source_key' => 'route:admin.profile-builder.index',
                'source_type' => 'route', 'permission_key' => 'website.view', 'parent_id' => $website->id,
                'sort_order' => 3, 'is_visible' => true,
            ]);
        }

        $footer = NavigationMenuItem::query()
            ->where('menu', 'dashboard')->where('area', 'dashboard')
            ->where(function ($query): void {
                $query->where('label', 'Footer Manager')->orWhere('url', '/admin/settings?section=footer');
            })->first();

        $values = [
            'menu' => 'dashboard', 'parent_id' => $website->id, 'label' => 'Footer Manager', 'label_override' => null,
            'url' => '/admin/settings?section=footer', 'route_name' => null, 'target' => '_self',
            'icon' => 'fa-window-restore', 'is_visible' => true, 'sort_order' => 10, 'source_key' => null,
            'source_type' => 'external_link', 'area' => 'dashboard', 'permission_key' => 'settings.manage',
        ];
        if ($footer) $footer->update($values); else NavigationMenuItem::create($values);

        NavigationMenuItem::query()->where('menu', 'dashboard')->where('area', 'dashboard')
            ->where('parent_id', $website->id)->where('source_type', 'folder')->delete();
        cache()->forget('fuelfree.dashboard_navigation');
    }

    public function down(): void {}
};
