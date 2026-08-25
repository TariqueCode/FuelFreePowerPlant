<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PowerPlant;
use Illuminate\View\View;

class PowerPlantPageController extends Controller
{
    public function show(string $slug): View
    {
        $plant = PowerPlant::query()->where('slug', $slug)->firstOrFail();
        $plants = PowerPlant::query()->whereKeyNot($plant->getKey())->latest()->take(6)->get(['name','slug','location','capacity_kw','technology','status','image_path']);
        $pages = CmsPage::query()->where('is_published', true)->where('slug', '!=', 'home')->orderBy('title')->get(['title','slug']);

        return view('power-plants.show', compact('plant', 'plants', 'pages'));
    }
}
