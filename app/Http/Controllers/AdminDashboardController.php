<?php

namespace App\Http\Controllers;

use App\Models\CareerApplication;
use App\Models\Inquiry;
use App\Models\SiteContentItem;
use App\Models\SitePopup;
use App\Models\SiteSlider;
use Illuminate\View\View;

class AdminDashboardController
{
    public function __invoke(): View
    {
        $contentTotal=SiteContentItem::count();
        $published=SiteContentItem::where('status','published')->count();
        $drafts=SiteContentItem::where('status','draft')->count();
        $news=SiteContentItem::whereIn('type',['news','announcement'])->count();
        $gallery=SiteContentItem::where('type','gallery')->count();
        $company=SiteContentItem::where('type','company')->count();
        $sliders=SiteSlider::count();
        $popups=SitePopup::count();
        $applications=CareerApplication::count();
        $newApplications=CareerApplication::where('status','new')->count();
        $inquiries=Inquiry::count();
        return view('admin.control-center',compact(
            'contentTotal','published','drafts','news','gallery','company',
            'sliders','popups','applications','newApplications','inquiries'
        ));
    }
}