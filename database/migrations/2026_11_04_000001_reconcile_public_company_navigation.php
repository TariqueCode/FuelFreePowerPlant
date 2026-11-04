<?php

use App\\Models\\CmsPage;
use App\\Models\\NavigationMenuItem;
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('navigation_menu_items') || ! Schema::hasTable('cms_pages')) {
            return;
        }

        $pageSlugs = [
            'about-us',
            'our-offers',
            'return-on-investment-roi',
            'profit-analysis-1-mw-plant',
            'profit-analysis-normal-calculation-1-mw-plant',
            'profit-analysis-conservative-calculation-1-mw-plant',
            'profit-analysis-five-year-financial-analysis-1-mw-plant',
            'profit-analysis-5-year-cash-flow-comparison',
            'profit-analysis-scenario-share',
            'profit-analysis-5-year-cash-flow-summary-1-mw-project',
            'our-technology',
            'our-technology-2',
            'our-technology-3',
            'our-technology-4',
        ];

        $pages = CmsPage::query()
            ->whereIn('slug', $pageSlugs)
            ->where('is_published', true)
            ->get(['id', 'title', 'slug'])
            ->keyBy('slug');

        $root = static function (string $label, int $order): ?NavigationMenuItem {
            $item = NavigationMenuItem::query()
                ->where('menu', 'main')
                ->whereNull('parent_id')
                ->where('source_type', 'folder')
                ->where('label', $label)
                ->orderBy('id')
                ->first();

            if (! $item) {
                $item = NavigationMenuItem::query()
                    ->where('menu', 'main')
                    ->where('label', $label)
                    ->orderBy('id')
                    ->first();
            }

            if ($item) {
                $item->update([
                    'menu' => 'main',
                    'parent_id' => null,
                    'sort_order' => $order,
                    'is_visible' => true,
                    'target' => '_self',
                ]);
            }

            return $item?->fresh();
        };

        $company = $root('Company', 3);
        if (! $company) {
            $company = NavigationMenuItem::query()->create([
                'menu' => 'main',
                'parent_id' => null,
                'label' => 'Company',
                'source_type' => 'folder',
                'target' => '_self',
                'is_visible' => true,
                'sort_order' => 3,
                'area' => 'public',
            ]);
        }

        $about = $pages->get('about-us');
        if ($about) {
            $existingAbout = NavigationMenuItem::query()
                ->where('menu', 'main')
                ->where(function ($query) use ($about): void {
                    $query->where('source_key', 'cms_page:'.$about->id)
                        ->orWhere('label', 'About us');
                })
                ->orderBy('id')
                ->first();

            $values = [
                'menu' => 'main',
                'parent_id' => null,
                'label' => $about->title,
                'url' => '/pages/'.$about->slug,
                'route_name' => 'cms.page',
                'source_key' => 'cms_page:'.$about->id,
                'source_type' => 'cms_page',
                'area' => 'public',
                'permission_key' => null,
                'target' => '_self',
                'is_visible' => true,
                'sort_order' => 2,
            ];

            if ($existingAbout) {
                $existingAbout->update($values);
            } else {
                NavigationMenuItem::query()->create($values);
            }
        }

        $message = NavigationMenuItem::query()
            ->where('menu', 'main')
            ->where('source_key', 'management_folder:3')
            ->first();
        if ($message) {
            $message->update(['parent_id' => null, 'label' => 'Message', 'sort_order' => 1, 'is_visible' => true]);
        }

        $rootOrders = [
            'Home' => 0,
            'Message' => 1,
            'About Us' => 2,
            'Company' => 3,
            'Board of Directors' => 4,
            'News & Event' => 5,
            'Gallery' => 6,
            'Career' => 7,
            'Contact' => 8,
        ];

        foreach ($rootOrders as $label => $order) {
            if ($label === 'About Us') {
                $item = NavigationMenuItem::query()->where('menu', 'main')->where('source_key', 'cms_page:'.($about->id ?? -1))->first();
            } elseif ($label === 'Message') {
                $item = $message;
            } else {
                $item = NavigationMenuItem::query()->where('menu', 'main')->whereNull('parent_id')->where('label', $label)->orderBy('id')->first();
            }

            if ($item) {
                $item->update(['parent_id' => null, 'sort_order' => $order, 'is_visible' => true]);
            }
        }

        $profitFolder = NavigationMenuItem::query()
            ->where('menu', 'main')->where('parent_id', $company->id)
            ->where('source_type', 'folder')->where('label', 'Profit Analysis')->first();
        if (! $profitFolder) {
            $profitFolder = NavigationMenuItem::query()->create([
                'menu' => 'main', 'parent_id' => $company->id, 'label' => 'Profit Analysis',
                'source_type' => 'folder', 'area' => 'public', 'target' => '_self',
                'is_visible' => true, 'sort_order' => 1,
            ]);
        } else {
            $profitFolder->update(['is_visible' => true, 'sort_order' => 1]);
        }

        $technologyFolder = NavigationMenuItem::query()
            ->where('menu', 'main')->where('parent_id', $company->id)
            ->where('source_type', 'folder')->where('label', 'Our Technology')->first();
        if (! $technologyFolder) {
            $technologyFolder = NavigationMenuItem::query()->create([
                'menu' => 'main', 'parent_id' => $company->id, 'label' => 'Our Technology',
                'source_type' => 'folder', 'area' => 'public', 'target' => '_self',
                'is_visible' => true, 'sort_order' => 2,
            ]);
        } else {
            $technologyFolder->update(['is_visible' => true, 'sort_order' => 2]);
        }

        $placePage = static function (CmsPage $page, int $parentId, int $order): void {
            $key = 'cms_page:'.$page->id;
            $item = NavigationMenuItem::query()
                ->where('menu', 'main')
                ->where('source_key', $key)
                ->orderBy('id')
                ->first();

            $values = [
                'menu' => 'main',
                'parent_id' => $parentId,
                'label' => $page->title,
                'url' => '/pages/'.$page->slug,
                'route_name' => 'cms.page',
                'source_key' => $key,
                'source_type' => 'cms_page',
                'area' => 'public',
                'permission_key' => null,
                'target' => '_self',
                'is_visible' => true,
                'sort_order' => $order,
            ];

            if ($item) {
                $item->update($values);
            } else {
                NavigationMenuItem::query()->create($values);
            }
        };

        if ($pages->has('our-offers')) {
            $placePage($pages->get('our-offers'), (int) $company->id, 0);
        }

        $profitSlugs = [
            'return-on-investment-roi',
            'profit-analysis-1-mw-plant',
            'profit-analysis-normal-calculation-1-mw-plant',
            'profit-analysis-conservative-calculation-1-mw-plant',
            'profit-analysis-five-year-financial-analysis-1-mw-plant',
            'profit-analysis-5-year-cash-flow-comparison',
            'profit-analysis-scenario-share',
            'profit-analysis-5-year-cash-flow-summary-1-mw-project',
        ];

        foreach ($profitSlugs as $order => $slug) {
            if ($pages->has($slug)) {
                $placePage($pages->get($slug), (int) $profitFolder->id, $order);
            }
        }

        foreach (['our-technology', 'our-technology-2', 'our-technology-3', 'our-technology-4'] as $order => $slug) {
            if ($pages->has($slug)) {
                $placePage($pages->get($slug), (int) $technologyFolder->id, $order);
            }
        }

        // Preserve the three existing document links and place them after the CMS folders.
        $documentOrders = [
            'Incorporation Certificate' => 3,
            'Financial Profit Analysis' => 4,
            'Project Profile' => 5,
        ];

        foreach ($documentOrders as $label => $order) {
            NavigationMenuItem::query()
                ->where('menu', 'main')
                ->where('parent_id', $company->id)
                ->where('label', $label)
                ->update(['sort_order' => $order, 'is_visible' => true]);
        }

        // Ensure the company folder remains visible and correctly ordered.
        $company->update(['parent_id' => null, 'sort_order' => 3, 'is_visible' => true]);

        app(\\App\\Services\\PublicNavigationService::class)->clear('main');
    }

    public function down(): void
    {
        // Intentionally non-destructive: navigation changes are content configuration.
    }
};
