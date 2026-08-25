<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use App\Models\PowerPlant;
use App\Models\SiteContentItem;
use App\Models\SitePopup;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $contentTotal=SiteContentItem::count();
        $published=SiteContentItem::where('status','published')->count();
        $drafts=SiteContentItem::where('status','draft')->count();
        $news=SiteContentItem::where('type','news')->count();
        $resources=SiteContentItem::where('type','resource')->count();
        $gallery=SiteContentItem::where('type','gallery')->count();
        $management=SiteContentItem::where('type','management')->count();
        $sustainability=SiteContentItem::where('type','sustainability')->count();
        return view('admin.control-center',[
            'contentTotal'=>$contentTotal,'published'=>$published,'drafts'=>$drafts,
            'news'=>$news,'resources'=>$resources,'gallery'=>$gallery,'management'=>$management,
            'sustainability'=>$sustainability,'plants'=>PowerPlant::count(),
            'popups'=>SitePopup::count(),'mailAccounts'=>EmailAccount::count(),
        ]);
    }
}
