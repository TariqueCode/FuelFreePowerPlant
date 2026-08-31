<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SiteContentController extends Controller
{
    private array $types = ['company','news','gallery','announcement','resource'];
    private array $labels = ['company'=>'Company & About','management'=>'Management','news'=>'News & Notices','gallery'=>'Gallery','announcement'=>'Announcements','resource'=>'Resources'];

    public function index(Request $request): View|RedirectResponse
    {
        $type = $request->string('type')->toString();
        if ($type === 'gallery') return redirect()->route('admin.gallery.index');
        if ($type === 'management') return redirect()->route('admin.management.index');
        abort_unless($type === '' || in_array($type, $this->types, true), 404);

        $items = SiteContentItem::query()
            ->when($type, function ($q) use ($type) {
                return $type === 'news' ? $q->whereIn('type', ['news','announcement']) : $q->where('type', $type);
            })
            ->when($type === 'news', function ($q) use ($request) {
                $search = trim($request->string('q')->toString());
                $filter = $request->string('filter')->toString();
                $sort = $request->string('sort')->toString();
                return $q
                    ->when($search !== '', fn($query) => $query->where(function ($inner) use ($search) {
                        $inner->where('title', 'like', "%{$search}%")
                            ->orWhere('excerpt', 'like', "%{$search}%");
                    }))
                    ->when(in_array($filter, ['news','announcement'], true), fn($query) => $query->where('type', $filter))
                    ->when(in_array($filter, ['published','draft'], true), fn($query) => $query->where('status', $filter))
                    ->when($filter === 'featured', fn($query) => $query->where('is_featured', true))
                    ->when($sort === 'oldest', fn($query) => $query->orderBy('published_at')->orderBy('created_at'))
                    ->when($sort === 'updated', fn($query) => $query->orderByDesc('updated_at'))
                    ->when(!in_array($sort, ['oldest','updated'], true), fn($query) => $query->orderByDesc('published_at')->orderByDesc('created_at'));
            })
            ->when($type === 'company', fn($q) => $q->orderByRaw('CASE WHEN navigation_order IS NULL THEN 1 ELSE 0 END')->orderBy('navigation_order')->orderByDesc('created_at'))
            ->when($type !== '' && $type !== 'company' && $type !== 'news', fn($q) => $q->latest('created_at'))
            ->when($type === '', fn($q) => $q->latest('created_at'))
            ->paginate(20)->withQueryString();

        $title = $type ? ($this->labels[$type] ?? ucfirst($type)).' CMS' : 'Website Content';
        $publishedCount = $type === 'news'
            ? SiteContentItem::query()->whereIn('type', ['news','announcement'])->where('status', 'published')->count()
            : 0;

        return view('admin.site-content.index', compact('items','type','title','publishedCount'))
            ->with('types', $this->types)->with('labels', $this->labels);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $type = $request->string('type')->toString();
        if ($type === 'gallery') return redirect()->route('admin.gallery.create');
        abort_unless($type === '' || in_array($type, $this->types, true), 404);
        $item = new SiteContentItem(['type' => $type]);
        return view('admin.site-content.create', ['item'=>$item,'types'=>$this->types,'labels'=>$this->labels,'lockedType'=>$type ?: null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $item = new SiteContentItem();
        $this->saveItem($item, $request);
        $redirectType = in_array($item->type, ['news','announcement'], true) ? 'news' : $item->type;
        return redirect()->route('admin.site-content.index', ['type'=>$redirectType])->with('status', ($this->labels[$redirectType] ?? 'Website').' content created successfully.');
    }

    public function edit(SiteContentItem $item): View|RedirectResponse
    {
        abort_unless(in_array($item->type, array_merge($this->types, ['management']), true), 404);
        if ($item->type === 'gallery') return redirect()->route('admin.gallery.edit', $item);
        if ($item->type === 'management') return redirect()->route('admin.management.edit', $item);
        $lockedType = in_array($item->type, ['news','announcement'], true) ? 'news' : $item->type;
        return view('admin.site-content.create', ['item'=>$item,'types'=>$this->types,'labels'=>$this->labels,'lockedType'=>$lockedType]);
    }

    public function update(Request $request, SiteContentItem $item): RedirectResponse
    {
        $this->saveItem($item, $request);
        $redirectType = in_array($item->type, ['news','announcement'], true) ? 'news' : $item->type;
        return redirect()->route('admin.site-content.index', ['type'=>$redirectType])->with('status','Content updated successfully.');
    }

    public function destroy(SiteContentItem $item): RedirectResponse
    {
        $type = in_array($item->type, ['news','announcement'], true) ? 'news' : $item->type;
        foreach ([$item->image_path, $item->attachment_path] as $path) {
            if ($path) Storage::disk('public')->delete($path);
        }
        $item->delete();
        return redirect()->route('admin.site-content.index', ['type'=>$type])->with('status','Content deleted successfully.');
    }

    public function toggleNews(Request $request, SiteContentItem $item): RedirectResponse
    {
        abort_unless(in_array($item->type, ['news','announcement'], true), 404);
        abort_unless($request->user()->hasPermission('website.publish'), 403, 'Publishing website content requires publishing permission.');
        $item->status = $item->status === 'published' ? 'draft' : 'published';
        if ($item->status === 'published' && empty($item->published_at)) $item->published_at = now();
        $item->save();
        return redirect()->route('admin.site-content.index', ['type'=>'news'])->with('status', $item->status === 'published' ? 'Publication activated.' : 'Publication deactivated.');
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $data = $request->validate(['media'=>['required','file','mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov','max:'.$this->maxUploadKb()]]);
        $file = $data['media']->store('site-content/media','public');
        return response()->json(['url'=>Storage::disk('public')->url($file),'mime'=>$data['media']->getMimeType(),'name'=>$data['media']->getClientOriginalName()]);
    }

    private function uniqueSlug(string $slug, string $title, ?int $ignoreId, string $type): string
    {
        $base = Str::slug($slug !== '' ? $slug : $title);
        abort_if($base === '', 422, 'A valid content slug could not be generated.');

        $candidate = $base;
        $counter = 2;
        while (SiteContentItem::query()
            ->where('type', $type)
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$counter++;
        }

        return $candidate;
    }

    private function saveItem(SiteContentItem $item, Request $request): SiteContentItem
    {
        $data = $request->validate([
            'type'=>['required','in:company,news,announcement,gallery,resource'],
            'publication_type'=>['nullable','in:news,announcement'],
            'title'=>['required','string','max:255'],
            'slug'=>['nullable','string','max:255'],
            'excerpt'=>['nullable','string','max:1000'],
            'content'=>['nullable','string'],
            'image_path'=>['nullable','string','max:500'],
            'cover_alt'=>['nullable','string','max:255'],
            'status'=>['required','in:draft,published'],
            'published_at'=>['nullable','date'],
            'is_featured'=>['nullable','boolean'],
            'meta_title'=>['nullable','string','max:255'],
            'meta_description'=>['nullable','string','max:1000'],
            'show_in_navigation'=>['nullable','boolean'],
        ]);

        if (in_array($data['type'], ['news','announcement'], true) && !empty($data['publication_type'])) $data['type'] = $data['publication_type'];
        unset($data['publication_type']);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? '', $data['title'], $item->id, $data['type']);
        $data['is_featured'] = in_array($data['type'], ['news','announcement'], true) && (bool)($data['is_featured'] ?? false);
        $data['show_in_navigation'] = $data['type'] === 'company' && (bool)($data['show_in_navigation'] ?? false);

        if ($data['type'] === 'company' && $data['show_in_navigation'] && !($item->exists && $item->type === 'company' && $item->show_in_navigation)) {
            $data['navigation_order'] = (int)(SiteContentItem::query()->where('type','company')->where('show_in_navigation',true)->min('navigation_order') ?? 1) - 1;
        }
        if ($data['type'] !== 'company') $data['show_in_navigation'] = false;
        if ($data['status'] === 'published' && empty($data['published_at'])) $data['published_at'] = now();
        $this->guardPublishing($request, $data['status'] === 'published');
        $item->fill($data)->save();
        return $item;
    }

    private function guardPublishing(Request $request, bool $publishing): void
    {
        abort_unless(! $publishing || $request->user()->hasPermission('website.publish'), 403, 'Publishing website content requires publishing permission.');
    }

    private function maxUploadKb(): int
    {
        $mb=(int) \App\Models\SystemSetting::query()->where('key','uploads.content_media_max_mb')->value('value');
        return max(1,$mb ?: (int) config('fuelfree.upload.content_media_max_mb',100))*1024;
    }
}