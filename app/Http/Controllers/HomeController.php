<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SiteSlider;
use App\Models\SystemSetting;
use Illuminate\Http\Response;

class HomeController
{
    public function __invoke(): Response
    {
        $plants=PowerPlant::query()->orderByRaw("CASE WHEN status='operational' THEN 0 ELSE 1 END")->latest()->take(6)->get();
        $homePage=CmsPage::query()->where('slug','home')->where('is_published',true)->first();
        // Show both News and Notices/Announcements in the homepage News & Notices section.
        $content=SiteContentItem::published()->whereIn('type',['news','announcement'])->orderBy('sort_order')->latest('published_at')->get()->groupBy(fn ($item) => in_array($item->type, ['news','announcement'], true) ? 'news' : $item->type);
        $gallery=SiteContentItem::published()->where('type','gallery')->whereNotNull('image_path')->withCount('galleryMedia')->orderBy('sort_order')->latest('published_at')->get();
        $sliders=SiteSlider::active()->get();
        $settings=SystemSetting::query()->pluck('value','key')->all();
        $brand=['name'=>$settings['company.name']??config('fuelfree.company.name'),'domain'=>$settings['company.domain']??config('fuelfree.company.domain'),'tagline'=>$settings['company.tagline']??config('fuelfree.company.tagline'),'logo_path'=>$settings['company.logo_path']??null];
        $newsLimit=max(1,min(12,(int)($settings['home.news_limit']??3))); $galleryLimit=max(1,min(12,(int)($settings['home.gallery_limit']??4)));
        $content['news']=$content['news']->take($newsLimit);
        $gallery=$gallery->take($galleryLimit);
        $stats=['projects'=>PowerPlant::query()->count(),'capacity_mw'=>round((float)PowerPlant::query()->sum('capacity_kw')/1000,2),'operational'=>PowerPlant::query()->whereRaw('LOWER(status)=?', ['operational'])->count()];

        return response(view('home-v3',compact('plants','homePage','stats','content','brand','gallery','sliders'))->render());
    }
}
