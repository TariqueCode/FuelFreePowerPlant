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
        $user = auth()->user();

        // Dashboard cards must never reveal counts from modules the administrator
        // is not allowed to view. Keep the dashboard useful without leaking data.
        $can = static fn (string $permission): bool => $user->hasPermission($permission);

        $contentTotal = $published = $drafts = $news = $gallery = $company = $sliders = $popups = 0;
        if ($can('website.view')) {
            $contentTotal = SiteContentItem::count();
            $published = SiteContentItem::where('status', 'published')->count();
            $drafts = SiteContentItem::where('status', 'draft')->count();
            $news = SiteContentItem::whereIn('type', ['news', 'announcement'])->count();
            $gallery = SiteContentItem::where('type', 'gallery')->count();
            $company = SiteContentItem::where('type', 'company')->count();
            $sliders = SiteSlider::count();
            $popups = SitePopup::count();
        }

        $applications = $newApplications = 0;
        if ($can('career.view')) {
            $applications = CareerApplication::count();
            $newApplications = CareerApplication::where('status', 'new')->count();
        }

        $inquiries = $can('inquiries.view') ? Inquiry::count() : 0;

        return view('admin.control-center', compact(
            'contentTotal',
            'published',
            'drafts',
            'news',
            'gallery',
            'company',
            'sliders',
            'popups',
            'applications',
            'newApplications',
            'inquiries'
        ));
    }
}
