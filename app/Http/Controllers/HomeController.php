<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SitePopup;
use App\Models\SystemSetting;
use Illuminate\Http\Response;

class HomeController
{
    public function __invoke(): Response
    {
        $plants=PowerPlant::query()->orderByRaw("CASE WHEN status='operational' THEN 0 ELSE 1 END")->latest()->take(6)->get();
        $homePage=CmsPage::query()->where('slug','home')->where('is_published',true)->first();
        $pages=CmsPage::query()->where('is_published',true)->where('slug','!=','home')->orderBy('title')->get(['title','slug']);
        $content=SiteContentItem::published()->orderBy('sort_order')->latest('published_at')->get()->groupBy('type');
        $gallery=SiteContentItem::published()->where('type','gallery')->orderBy('sort_order')->latest('published_at')->get();
        $heroImages=SiteContentItem::published()->whereNotNull('image_path')->whereIn('type',['gallery','company'])->orderBy('sort_order')->latest('published_at')->take(8)->get(['title','image_path','excerpt']);
        $settings=SystemSetting::query()->pluck('value','key')->all();
        $brand=['name'=>$settings['company.name']??config('fuelfree.company.name'),'domain'=>$settings['company.domain']??config('fuelfree.company.domain'),'tagline'=>$settings['company.tagline']??config('fuelfree.company.tagline'),'logo_path'=>$settings['company.logo_path']??null];
        $stats=['projects'=>PowerPlant::query()->count(),'capacity_mw'=>round((float)PowerPlant::query()->sum('capacity_kw')/1000,2),'operational'=>PowerPlant::query()->whereRaw('LOWER(status)=?', ['operational'])->count()];
        $announcementPopup=SitePopup::active()->first();
        $html=view('home-v2',compact('plants','homePage','pages','stats','content','brand','announcementPopup','heroImages'))->render();
        $galleryHtml=view('partials.home-gallery',compact('gallery'))->render();
        return response(str_replace('</main>',$galleryHtml.'</main>',$html));
    }
}
