<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class NewsEventController extends Controller
{
    public function index(Request $request): View
    {
        $query = SiteContentItem::query()->whereIn('type', ['news', 'announcement'])->latest('updated_at');
        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%");
            });
        }
        return view('admin.news-event.index', ['items' => $query->paginate(20)->withQueryString(), 'search' => (string) $request->input('q', '')]);
    }

    public function create(): View
    {
        return view('admin.news-event.form', ['item' => new SiteContentItem(['type' => 'news', 'status' => 'draft']), 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateItem($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        $data['published_at'] = $data['status'] === 'published' ? now() : null;
        SiteContentItem::create($data);
        return redirect()->route('admin.news_and_event')->with('status', 'News & Event created successfully.');
    }

    public function edit(SiteContentItem $item): View
    {
        abort_unless(in_array($item->type, ['news', 'announcement'], true), 404);
        return view('admin.news-event.form', ['item' => $item, 'mode' => 'edit']);
    }

    public function update(Request $request, SiteContentItem $item): RedirectResponse
    {
        abort_unless(in_array($item->type, ['news', 'announcement'], true), 404);
        $data = $this->validateItem($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $item->id);
        $data['published_at'] = $data['status'] === 'published' ? ($item->published_at ?: now()) : null;
        $item->update($data);
        return redirect()->route('admin.news_and_event')->with('status', 'News & Event updated successfully.');
    }

    public function toggle(SiteContentItem $item): RedirectResponse
    {
        abort_unless(in_array($item->type, ['news', 'announcement'], true), 404);
        $published = $item->status !== 'published';
        $item->update(['status' => $published ? 'published' : 'draft', 'published_at' => $published ? ($item->published_at ?: now()) : null]);
        return back()->with('status', $published ? 'Published successfully.' : 'Moved to draft successfully.');
    }

    public function destroy(SiteContentItem $item): RedirectResponse
    {
        abort_unless(in_array($item->type, ['news', 'announcement'], true), 404);
        $item->delete();
        return back()->with('status', 'News & Event deleted successfully.');
    }

    private function validateItem(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:news,announcement'],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ]);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['use_global_framework'] = true;
        $data['use_global_header'] = true;
        $data['use_global_footer'] = true;
        $data['builder_blocks'] = [];
        return $data;
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        abort_if($base === '', 422, 'A valid slug could not be generated.');
        $slug = $base; $counter = 2;
        while (SiteContentItem::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }
        return $slug;
    }
}
