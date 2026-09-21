<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicSiteController
{
    private function brand(): array
    {
        $settings=SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
        return ['name'=>$settings->get('company.name')?:config('fuelfree.company.name'),'logo_path'=>$settings->get('company.logo_path'),'tagline'=>$settings->get('company.tagline')?:config('fuelfree.company.tagline')];
    }

    public function showCompanyPage(string $slug): View|RedirectResponse
    {
        // About Us has a dedicated canonical route and is already included in
        // the sitemap. Keep the legacy company URL aligned with that canonical
        // page instead of creating a second /pages/about-us URL.
        if ($slug === 'about-us') {
            return redirect()->route('site.about', [], 301);
        }

        // Other legacy company URLs resolve to their Page Builder canonical URL.
        return redirect()->route('cms.page', ['slug' => $slug], 301);
    }

    public function show(string $section): View|RedirectResponse
    {
        $allowed=['about-us','plants','future-project','career','solutions','gallery'];
        abort_unless(in_array($section,$allowed,true),404);

        if (request()->routeIs('site.section')) {
            $canonicalRoutes = [
                'about-us' => 'site.about',
                'plants' => 'site.plants',
                'future-project' => 'site.future-project',
                'career' => 'site.career',
                'solutions' => 'site.solutions',
                'gallery' => 'site.gallery',
            ];

            return redirect()->route($canonicalRoutes[$section], [], 301);
        }

        $brand=$this->brand();
        if($section==='gallery'){$galleries=SiteContentItem::query()->where('type','gallery')->where('status','published')->withCount('galleryMedia')->orderBy('sort_order')->latest('created_at')->get();return view('gallery.index',compact('galleries','brand'));}
        if($section==='about-us'){
            $aboutItem=CmsPage::query()->where('slug','about-us')->where('is_published',true)->firstOrFail();
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
        $item=SiteContentItem::query()->where('type','gallery')->where(function($q) use ($key){$q->where('slug',$key);if(ctype_digit($key))$q->orWhere('id',(int)$key);})->firstOrFail();
        abort_unless($item->status==='published',404);
        $brand=$this->brand();$item->load('galleryMedia');
        return view('gallery.show',compact('item','brand'));
    }
}
