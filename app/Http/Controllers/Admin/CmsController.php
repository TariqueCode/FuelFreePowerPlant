<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CmsController extends Controller
{
    public function index(Request $request): View
    {
        $pages = CmsPage::query()
            ->get()
            ->map(function (CmsPage $page) {
                $page->content_source = 'Global CMS';
                $page->edit_url = route('admin.cms.edit', $page);
                $page->toggle_url = route('admin.cms.toggle', $page);
                $page->delete_url = route('admin.cms.destroy', $page);
                $page->duplicate_url = route('admin.cms.duplicate', $page);
                return $page;
            })
            ->sortByDesc(fn ($page) => $page->updated_at?->timestamp ?? 0)
            ->values();

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pages = new LengthAwarePaginator(
            $pages->forPage($currentPage, $perPage)->values(),
            $pages->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.cms.index', ['pages' => $pages]);
    }

    public function create(): View
    {
        return view('admin.cms.page-builder', ['page' => new CmsPage(), 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePage($request);
        $this->guardPublishing($request, $data['is_published']);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        CmsPage::create($data);

        return redirect()->route('admin.cms.index')->with('status', 'Page created successfully.');
    }

    public function edit(CmsPage $page): View
    {
        return view('admin.cms.page-builder', ['page' => $page, 'mode' => 'edit']);
    }

    public function update(Request $request, CmsPage $page): RedirectResponse
    {
        $data = $this->validatePage($request);
        $this->guardPublishing($request, $data['is_published']);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $page->id);
        $page->update($data);

        return redirect()->route('admin.cms.index')->with('status', 'Page updated successfully.');
    }

    public function togglePublication(Request $request, CmsPage $page): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('cms.publish'), 403, 'Publishing pages requires publishing permission.');
        $page->update(['is_published' => !$page->is_published]);

        return redirect()->route('admin.cms.index')->with('status', $page->is_published ? 'Page published successfully.' : 'Page unpublished successfully.');
    }

    public function destroy(CmsPage $page): RedirectResponse
    {
        $page->delete();
        return back()->with('status', 'Page deleted successfully.');
    }

    public function duplicate(CmsPage $page): RedirectResponse
    {
        $copy = $page->replicate();
        $copy->title = Str::limit($page->title . ' Copy', 180, '');
        $copy->slug = $this->uniqueSlug($page->slug . '-copy');
        $copy->is_published = false;
        $copy->use_global_framework = true;
        $copy->use_global_header = true;
        $copy->use_global_footer = true;
        $copy->save();

        return redirect()->route('admin.cms.edit', $copy)->with('status', 'Draft copy created.');
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $data = $request->validate([
            'media' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov', 'max:' . $this->maxUploadKb()],
        ]);
        $file = $data['media'];
        $path = $file->store('site-content/media', 'public');
        return response()->json(['path' => $path, 'url' => Storage::disk('public')->url($path), 'type' => str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image']);
    }

    private function validatePage(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'use_global_framework' => ['nullable', 'boolean'],
            'use_global_header' => ['nullable', 'boolean'],
            'use_global_footer' => ['nullable', 'boolean'],
        ]);
    }

    private function guardPublishing(Request $request, bool $publishing): void
    {
        if ($publishing) {
            abort_unless($request->user()->hasPermission('cms.publish'), 403, 'Publishing pages requires publishing permission.');
        }
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $slug = Str::slug($source) ?: 'page';
        $base = $slug;
        $counter = 2;

        // These slugs are owned by dedicated public routes. New CMS pages
        // must not claim them and create competing public URLs.
        $reservedSlugs = [
            'about-us',
            'plants',
            'future-project',
            'career',
            'solutions',
            'gallery',
            'news-and-event',
            'sustainability',
            'contact',
            'management',
        ];

        while (
            in_array($slug, $reservedSlugs, true)
            || CmsPage::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }
        return $slug;
    }

    private function maxUploadKb(): int
    {
        $value = (int) SystemSetting::query()->where('key', 'media.max_upload_kb')->value('value');
        return max(1024, $value ?: 20480);
    }
}
