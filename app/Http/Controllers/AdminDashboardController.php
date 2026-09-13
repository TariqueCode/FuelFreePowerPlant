<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CareerApplication;
use App\Models\Inquiry;
use App\Models\SiteContentItem;
use App\Models\SitePopup;
use App\Models\SiteSlider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class AdminDashboardController
{
    public function __invoke(): View
    {
        $user = auth()->user();
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

        $recentActivity = collect();
        if ($can('audit.view')) {
            $recentActivity = AuditLog::with('user:id,name')->latest()->limit(4)->get();
        }

        $platformStatus = [
            'website' => true,
            'database' => false,
            'mail' => filled(config('mail.mailers.smtp.host')),
            'storage' => is_writable(storage_path('app/public')),
        ];

        try {
            DB::connection()->getPdo();
            $platformStatus['database'] = true;
        } catch (Throwable) {
            $platformStatus['database'] = false;
        }

        $storageBytes = Cache::remember('admin.dashboard.storage_bytes', 60, static function (): int {
            $root = storage_path('app/public');
            if (! is_dir($root)) {
                return 0;
            }

            $bytes = 0;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $bytes += $file->getSize();
                }
            }

            return $bytes;
        });

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
            'inquiries',
            'recentActivity',
            'platformStatus',
            'storageBytes'
        ));
    }
}
