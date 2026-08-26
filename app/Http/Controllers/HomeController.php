<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\Response;

class HomeController
{
    public function __invoke(): Response
    {
        $plants=PowerPlant::query()->orderByRaw("CASE WHEN status='operational' THEN 0 ELSE 1 END")->latest()->take(6)->get();
        $homePage=CmsPage::query()->where('slug','home')->where('is_published',true)->first();
        $content=SiteContentItem::published()->whereIn('type',['news'])->orderBy('sort_order')->latest('published_at')->get()->groupBy('type');
        $gallery=SiteContentItem::published()->where('type','gallery')->whereNotNull('image_path')->orderBy('sort_order')->latest('published_at')->get();
        $settings=SystemSetting::query()->pluck('value','key')->all();
        $brand=['name'=>$settings['company.name']??config('fuelfree.company.name'),'domain'=>$settings['company.domain']??config('fuelfree.company.domain'),'tagline'=>$settings['company.tagline']??config('fuelfree.company.tagline'),'logo_path'=>$settings['company.logo_path']??null];
        $stats=['projects'=>PowerPlant::query()->count(),'capacity_mw'=>round((float)PowerPlant::query()->sum('capacity_kw')/1000,2),'operational'=>PowerPlant::query()->whereRaw('LOWER(status)=?', ['operational'])->count()];

        $html=view('home-v3',compact('plants','homePage','stats','content','brand','gallery'))->render();
        $sharedHeader=view('partials.home-header')->render();
        $sharedFooter=view('partials.home-footer')->render();

        $html=preg_replace('~<header class="header">.*?</header>~s',$sharedHeader,$html,1);
        $html=preg_replace('~<footer class="footer">.*?</footer>~s',$sharedFooter,$html,1);

        return response($html);
    }
}
