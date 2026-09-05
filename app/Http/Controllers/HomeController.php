<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\HomepageSection;
use App\Models\SiteContentItem;
use App\Models\SiteSlider;
use App\Models\SystemSetting;
use Illuminate\Http\Response;

class HomeController
{
    public function __invoke(): Response
    {
        $homePage=CmsPage::query()->where('slug','home')->where('is_published',true)->first();
        $content=SiteContentItem::published()->whereIn('type',['news','announcement'])->orderBy('sort_order')->latest('published_at')->get()->groupBy(fn ($item) => in_array($item->type, ['news','announcement'], true) ? 'news' : $item->type);
        $gallery=SiteContentItem::published()->where('type','gallery')->whereNotNull('image_path')->withCount('galleryMedia')->orderBy('sort_order')->latest('published_at')->get();
        $sliders=SiteSlider::active()->get();
        $settings=SystemSetting::query()->pluck('value','key')->all();
        $brand=['name'=>$settings['company.name']??config('fuelfree.company.name'),'domain'=>$settings['company.domain']??config('fuelfree.company.domain'),'tagline'=>$settings['company.tagline']??config('fuelfree.company.tagline'),'logo_path'=>$settings['company.logo_path']??null];

        $configuredSections = HomepageSection::query()->ordered()->get();
        $normalizeSectionSettings = static function ($value): array {
            if (is_array($value)) {
                return $value;
            }
            if (is_string($value) && trim($value) !== '') {
                $decoded = json_decode($value, true);
                return is_array($decoded) ? $decoded : [];
            }
            return [];
        };
        $sectionSettings = $configuredSections->mapWithKeys(
            fn ($section) => [$section->key => $normalizeSectionSettings($section->settings)]
        );

        $sectionSettings = $sectionSettings->map(function (array $settings): array {
            $layout = $settings['layout'] ?? 'left';
            $settings['layout'] = in_array($layout, ['left', 'center', 'right'], true)
                ? $layout
                : 'left';
            return $settings;
        });
        $sectionOrder = $configuredSections->pluck('key')->all();
        $enabledSections = $configuredSections->where('is_enabled', true)->pluck('key')->flip();
        $home = [
            'slider' => isset($enabledSections['hero']),
            'welcome' => isset($enabledSections['welcome']),
            'statistics' => false,
            'projects' => false,
            'management' => isset($enabledSections['management']),
            'news' => isset($enabledSections['news']),
            'gallery' => isset($enabledSections['gallery']),
            'cta' => isset($enabledSections['cta']),
            'section_order' => array_values(array_filter($sectionOrder, fn ($key) => ! in_array($key, ['statistics','projects'], true))) ?: ['hero','welcome','management','news','gallery','cta'],
        ];

        $managementLimit = max(1, min(100, (int) data_get($sectionSettings->get('management', []), 'limit', 4)));
        $newsLimit=max(1,min(100,(int)($sectionSettings['news']['limit']??3)));
        $galleryLimit=max(1,min(100,(int)($sectionSettings['gallery']['limit']??4)));
        $resolveIds = static fn (array $settings): array => array_values(array_unique(array_filter(array_map('intval', $settings['ids'] ?? []))));
        $applySelection = static function ($query, array $settings, int $limit) use ($resolveIds) {
            if (($settings['mode'] ?? 'latest') === 'selected') {
                $ids = $resolveIds($settings);
                if (!$ids) {
                    return $query->whereRaw('1 = 0')->take($limit)->get();
                }
                $position = array_flip($ids);
                return $query->whereIn('id', $ids)->get()->sortBy(fn ($item) => $position[(int) $item->id] ?? PHP_INT_MAX)->take($limit)->values();
            }
            return $query->take($limit)->get();
        };

        $newsSettings = $sectionSettings['news'] ?? [];
        $managementSettings = $sectionSettings['management'] ?? [];
        $gallerySettings = $sectionSettings['gallery'] ?? [];
        $welcomeSettings = $sectionSettings['welcome'] ?? [];
        $welcomeManagementIds = array_values(array_unique(array_filter(array_map('intval', (array) ($welcomeSettings['management_ids'] ?? [])))));
        $welcomeManagement = $welcomeManagementIds
            ? SiteContentItem::published()->where('type','management')->whereIn('id', $welcomeManagementIds)->get()->sortBy(fn ($item) => array_search((int) $item->id, $welcomeManagementIds, true))->take(2)->values()
            : collect();
        $welcomeEyebrow = trim((string) ($welcomeSettings['eyebrow'] ?? ''));
        $welcomeTitle = trim((string) ($welcomeSettings['title'] ?? ($homePage?->title ?? 'Building a stronger energy future.')));
        $welcomeContent = trim((string) ($welcomeSettings['content'] ?? ''));
        $welcomeSignoff = trim((string) ($welcomeSettings['signoff'] ?? ''));
        if ($welcomeContent === '') {
            $welcomeContent = trim(strip_tags((string) ($homePage?->content ?? '')));
        }
        $welcomePreviewWords = max(20, min(500, (int) ($welcomeSettings['preview_words'] ?? 180)));
        $welcomeMoreWords = max(20, min(2000, (int) ($welcomeSettings['more_words'] ?? 900)));
        $welcomeShowFull = (bool) ($welcomeSettings['show_full'] ?? false);
        $requestedWelcomeLayout = $welcomeSettings['layout'] ?? 'left';
        $welcomeLayout = in_array($requestedWelcomeLayout, ['left','center','right'], true) ? $requestedWelcomeLayout : 'left';
        $welcomeWords = preg_split('/\s+/u', trim($welcomeContent), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $welcomePreview = $welcomeShowFull ? $welcomeContent : implode(' ', array_slice($welcomeWords, 0, $welcomePreviewWords));
        $welcomeRemaining = $welcomeShowFull ? '' : implode(' ', array_slice($welcomeWords, $welcomePreviewWords, $welcomeMoreWords));
        $welcomeHasMore = $welcomeRemaining !== '';

        $content['news'] = $applySelection(
            SiteContentItem::published()->whereIn('type',['news','announcement'])->orderBy('sort_order')->latest('published_at'),
            $newsSettings,
            $newsLimit
        );
        $managementQuery = SiteContentItem::published()
            ->where('type','management')
            ->orderBy('sort_order')
            ->orderBy('title');

        if (($managementSettings['mode'] ?? 'latest') === 'selected') {
            $homeManagement = $applySelection($managementQuery, $managementSettings, $managementLimit);
        } else {
            $homeManagement = SiteContentItem::published()
                ->where('type', 'management')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->limit(100)
                ->get()
                ->take($managementLimit)
                ->values();
        }

        $welcomeManagement = $welcomeManagement
            ->concat($homeManagement)
            ->unique('id')
            ->take($managementLimit)
            ->values();

        $gallery = $applySelection(
            SiteContentItem::published()->where('type','gallery')->whereNotNull('image_path')->withCount('galleryMedia')->orderBy('sort_order')->latest('published_at'),
            $gallerySettings,
            $galleryLimit
        );

        return response(view('home-v3',compact('homePage','content','brand','gallery','sliders','home','homeManagement','welcomeManagement','welcomeEyebrow','welcomeTitle','welcomeContent','welcomeSignoff','welcomePreviewWords','welcomeMoreWords','welcomeShowFull','welcomeLayout','welcomePreview','welcomeRemaining','welcomeHasMore','sectionSettings'))->render())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-FFP-Homepage-Source', 'home-v3-board-of-directors');
    }
}
