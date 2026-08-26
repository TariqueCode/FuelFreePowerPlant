<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteContentController extends Controller
{
    private array $types = ['company', 'management', 'news', 'sustainability', 'gallery', 'resource', 'announcement', 'solution'];

    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();
        $items = SiteContentItem::query()->when($type, fn ($q) => $q->where('type', $type))->orderBy('sort_order')->latest()->paginate(20)->withQueryString();
        return view('admin.site-content.index', compact('items', 'type'))->with('types', $this->types);
    }

    public function create(): View { return view('admin.site-content.create', ['item' => new SiteContentItem(), 'types' => $this->types]); }

    public function store(Request $request): RedirectResponse
    {
        $this->saveItem(new SiteContentItem(), $request);
        return redirect()->route('admin.site-content.index')->with('status', 'Website content created successfully.');
    }

    public function edit(SiteContentItem $item): View { return view('admin.site-content.create', ['item' => $item, 'types' => $this->types]); }

    public function update(Request $request, SiteContentItem $item): RedirectResponse
    {
        $this->saveItem($item, $request);
        return redirect()->route('admin.site-content.index')->with('status', 'Website content updated successfully.');
    }

    public function destroy(SiteContentItem $item): RedirectResponse
    {
        $item->delete();
        return back()->with('status', 'Website content deleted.');
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $data = $request->validate(['media' => ['required','file','mimes:jpg,jpeg,png,webp,gif,mp4,webm','max:20480']]);
        $file = $data['media']->store('site-content/media', 'public');
        return response()->json(['url' => Storage::disk('public')->url($file), 'mime' => $data['media']->getMimeType(), 'name' => $data['media']->getClientOriginalName()]);
    }

    private function saveItem(SiteContentItem $item, Request $request): SiteContentItem
    {
        $data = $request->validate([
            'type' => ['required', 'in:company,management,news,sustainability,gallery,resource,announcement,solution'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
        ]);
        if (($data['slug'] ?? '') === '') $data['slug'] = str($data['title'])->slug();
        $data['sort_order'] ??= 0;
        $item->fill($data)->save();
        return $item;
    }
}
