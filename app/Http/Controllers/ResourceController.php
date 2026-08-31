<?php

namespace App\Http\Controllers;

use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ResourceController extends Controller
{
    private function brand(): array
    {
        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        return [
            'name' => $settings['company.name'] ?? config('fuelfree.company.name'),
            'domain' => $settings['company.domain'] ?? config('fuelfree.company.domain'),
            'tagline' => $settings['company.tagline'] ?? config('fuelfree.company.tagline'),
            'logo_path' => $settings['company.logo_path'] ?? null,
        ];
    }

    public function index(): View
    {
        $resources = SiteContentItem::published()
            ->whereIn('type', ['resource', 'resources'])
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('resources.index', ['resources' => $resources, 'brand' => $this->brand()]);
    }

    public function download(string $slug): Response
    {
        $resource = SiteContentItem::published()
            ->whereIn('type', ['resource', 'resources'])
            ->where('slug', $slug)
            ->firstOrFail();

        abort_unless($resource->attachment_path, 404);
        abort_unless(Storage::disk('public')->exists($resource->attachment_path), 404);

        return response()->download(
            Storage::disk('public')->path($resource->attachment_path),
            $resource->attachment_name ?: basename($resource->attachment_path),
            ['Content-Type' => $resource->attachment_mime ?: 'application/pdf']
        );
    }


    public function download(string $slug): Response
    {
        $resource = SiteContentItem::published()
            ->whereIn('type', ['resource', 'resources'])
            ->where('slug', $slug)
            ->firstOrFail();

        abort_unless($resource->attachment_path, 404);
        $disk = Storage::disk('public');
        abort_unless($disk->exists($resource->attachment_path), 404);

        return $disk->response(
            $resource->attachment_path,
            $resource->attachment_name ?: basename($resource->attachment_path),
            ['Content-Type' => $resource->attachment_mime ?: 'application/pdf']
        );
    }

    public function show(string $slug): View
    {
        $resource = SiteContentItem::published()
            ->whereIn('type', ['resource', 'resources'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = SiteContentItem::published()
            ->whereIn('type', ['resource', 'resources'])
            ->where('id', '!=', $resource->id)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        return view('resources.show', ['resource' => $resource, 'related' => $related, 'brand' => $this->brand()]);
    }
}
