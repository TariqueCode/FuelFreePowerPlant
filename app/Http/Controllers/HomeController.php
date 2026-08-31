<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\HomepageSection;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SiteSlider;
use App\Models\SystemSetting;
use Illuminate\Http\Response;

class HomeController
{
    public function __invoke(): Response
    {
        $plants=PowerPlant::query()->orderByRaw("CASE WHEN status='operational' THEN 0 ELSE 1 END")->latest()->get();
        $homePage=CmsPage::query()->where('slug','home')->where('is_published',true)->first();
        $content=SiteContentItem::published()->whereIn('type',['news','announcement'])->orderBy('sort_order')->latest('published_at')->get()->groupBy(fn ($item) => in_array($item->type, ['news','announcement'], true) ? 'news' : $item->type);
        $gallery=SiteContentItem::published()->where('type','gallery')->whereNotNull('image_path')->withCount('galleryMedia')->orderBy('sort_order')->latest('published_at')->get();
        $sliders=SiteSlider::active()->get();
        $settings=SystemSetting::query()->pluck('value','key')->all();
        $brand=['name'=>$settings['company.name']??config('fuelfree.company.name'),'domain'=>$settings['company.domain']??config('fuelfree.company.domain'),'tagline'=>$settings['company.tagline']??config('fuelfree.company.tagline'),'logo_path'=>$settings['company.logo_path']??null];

        $configuredSections = HomepageSection::query()->ordered()->get();
        $sectionSettings = $configuredSections->mapWithKeys(fn ($section) => [$section->key => is_array($section->settings) ? $section->settings : []]);
        $sectionOrder = $configuredSections->pluck('key')->all();
        $enabledSections = $configuredSections->where('is_enabled', true)->pluck('key')->flip();
        $home = [
            'slider' => isset($enabledSections['hero']),
            'welcome' => isset($enabledSections['welcome']),
            'statistics' => isset($enabledSections['statistics']),
            'projects' => isset($enabledSections['projects']),
            'management' => isset($enabledSections['management']),
            'news' => isset($enabledSections['news']),
            'gallery' => isset($enabledSections['gallery']),
            'cta' => isset($enabledSections['cta']),
            'section_order' => $sectionOrder ?: ['hero','welcome','statistics','projects','management','news','gallery','cta'],
        ];

        $projectsLimit=max(1,min(100,(int)($sectionSettings['projects']['limit']??6)));
        $managementLimit=max(1,min(100,(int)($sectionSettings['management']['limit']??4)));
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
        $projectSettings = $sectionSettings['projects'] ?? [];
        $managementSettings = $sectionSettings['management'] ?? [];
        $gallerySettings = $sectionSettings['gallery'] ?? [];
        $welcomeSettings = $sectionSettings['welcome'] ?? [];
        $welcomeEyebrow = trim((string) ($welcomeSettings['eyebrow'] ?? ''));
        $welcomeTitle = trim((string) ($welcomeSettings['title'] ?? ($homePage?->title ?? 'Building a stronger energy future.')));
        $welcomeContent = trim((string) ($welcomeSettings['content'] ?? ''));
        if ($welcomeContent === '') {
            $welcomeContent = trim(strip_tags((string) ($homePage?->content ?? '')));
        }
        $welcomePreviewWords = max(20, min(500, (int) ($welcomeSettings['preview_words'] ?? 180)));
        $welcomeMoreWords = max(20, min(2000, (int) ($welcomeSettings['more_words'] ?? 900)));
        $welcomeShowFull = (bool) ($welcomeSettings['show_full'] ?? false);
        $welcomeLayout = in_array(($welcomeSettings['layout'] ?? 'left'), ['left','center','right'], true) ? $welcomeSettings['layout'] : 'left';
        $welcomePreview = $welcomeShowFull ? $welcomeContent : \Illuminate\Support\Str::words($welcomeContent, $welcomePreviewWords, '');
        $welcomeRemaining = $welcomeShowFull ? '' : trim(\Illuminate\Support\Str::words(substr($welcomeContent, strlen($welcomePreview)), $welcomeMoreWords, ''));
        $welcomeRemaining = $welcomeRemaining !== '' ? trim($welcomeRemaining) : '';


        $content['news'] = $applySelection(
            SiteContentItem::published()->whereIn('type',['news','announcement'])->orderBy('sort_order')->latest('published_at'),
            $newsSettings,
            $newsLimit
        );
        $plants = $applySelection(
            PowerPlant::query()->orderByRaw("CASE WHEN status='operational' THEN 0 ELSE 1 END")->latest(),
            $projectSettings,
            $projectsLimit
        );
        $homeManagement = $applySelection(
            SiteContentItem::published()->where('type','management')->orderBy('sort_order')->orderBy('title'),
            $managementSettings,
            $managementLimit
        );
        $gallery = $applySelection(
            SiteContentItem::published()->where('type','gallery')->whereNotNull('image_path')->withCount('galleryMedia')->orderBy('sort_order')->latest('published_at'),
            $gallerySettings,
            $galleryLimit
        );
        $stats=['projects'=>PowerPlant::query()->count(),'capacity_mw'=>round((float)PowerPlant::query()->sum('capacity_kw')/1000,2),'operational'=>PowerPlant::query()->whereRaw('LOWER(status)=?', ['operational'])->count()];

        return response(view('home-v3',compact('plants','homePage','stats','content','brand','gallery','sliders','home','homeManagement','welcomeEyebrow','welcomeTitle','welcomeContent','welcomePreviewWords','welcomeMoreWords','welcomeShowFull','welcomeLayout','welcomePreview','welcomeRemaining'))->render());
    }
}
