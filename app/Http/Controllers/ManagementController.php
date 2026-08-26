<?php

namespace App\Http\Controllers;

use App\Models\SiteContentItem;
use Illuminate\View\View;

class ManagementController extends Controller
{
    public function __invoke(): View
    {
        $members = SiteContentItem::query()
            ->where('type', 'management')
            ->published()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('management.index', compact('members'));
    }
}
