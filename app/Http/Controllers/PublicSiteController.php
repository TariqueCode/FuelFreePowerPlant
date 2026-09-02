<?php

namespace App\Http\Controllers;

use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\View\View;

class PublicSiteController
{
    private function brand(): array
    {
        $settings=SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
        return ['name'=>$settings->get('company.name')?:config('fuelfree.company.name'),'logo_path'=>$settings->get('company.logo_path'),'tagline'=>$settings->get('company.tagline')?:config('fuelfree.company.tagline')];
    }

    public function showCompanyPage(string $slug): View
    {
        $item=SiteContentItem::query()->where('type','company')->where('status','published')->where('slug',$slug)->firstOrFail();
        return view('site.company-page',['item'=>$item,'brand'=>$this->brand()]);
    }

    public function show(string $section): View
    {
        $allowed=['about-us','plants','future-project','career','solutions','gallery'];abort_unless(in_array($section,$allowed,true),404);
        $brand=$this->brand();
        if($section==='gallery'){$galleries=SiteContentItem::query()->where('type','gallery')->where('status','published')->withCount('galleryMedia')->orderBy('sort_order')->latest('created_at')->get();return view('gallery.index',compact('galleries','brand'));}
        if($section==='about-us'){
            $aboutItem=SiteContentItem::published()->where('type','company')->where('slug','about-us')->firstOrFail();
            return view('site.company-page',['item'=>$aboutItem,'brand'=>$brand,'backRoute'=>route('home'),'backLabel'=>'Back to Home']);
        }
        $items=collect();
        if($section==='plants'){$items=SiteContentItem::published()->where('type','plants')->orderBy('sort_order')->orderBy('title')->get();}
        elseif($section==='future-project'){$items=SiteContentItem::published()->where('type','future-project')->orderBy('sort_order')->orderBy('title')->get();}
        elseif($section==='career'){$items=SiteContentItem::published()->whereIn('type',['career','careers','job'])->orderBy('sort_order')->orderBy('title')->get();}
        elseif($section==='solutions'){$items=SiteContentItem::published()->where('type','solution')->orderBy('sort_order')->orderBy('title')->get();}
        $titles=['plants'=>config('fuelfree.projects.label','Projects & Our Plans'),'future-project'=>'Future Project','career'=>'Career','solutions'=>'Solutions'];
        return view('site.section',compact('section','titles','brand','items'));
    }

    public function showGallery(string $key): View
    {
        $item=SiteContentItem::query()
            ->where('type','gallery')
            ->where(function($q) use ($key) {
                $q->where('slug', $key);
                if (ctype_digit($key)) {
                    $q->orWhere('id', (int) $key);
                }
            })
            ->firstOrFail();

        abort_unless($item->status==='published',404);
        $brand=$this->brand();
        $item->load('galleryMedia');
        return view('gallery.show',compact('item','brand'));
    }
}
