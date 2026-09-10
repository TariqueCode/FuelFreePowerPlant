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
        $pages = CmsPage::query()->get()->map(function (CmsPage $page) {
            $page->content_source = 'Page Builder';
            $page->edit_url = route('admin.page-builder.edit', $page);
            $page->toggle_url = route('admin.page-builder.toggle', $page);
            $page->delete_url = route('admin.page-builder.destroy', $page);
            $page->duplicate_url = route('admin.page-builder.duplicate', $page);
            return $page;
        })->sortByDesc(fn ($page) => $page->updated_at?->timestamp ?? 0)->values();

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pages = new LengthAwarePaginator($pages->forPage($currentPage, $perPage)->values(), $pages->count(), $perPage, $currentPage, ['path' => $request->url(), 'query' => $request->query()]);

        return view('admin.cms.page-builder-index', ['pages' => $pages]);
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
        return redirect()->route('admin.page-builder.index')->with('status', 'Page created successfully.');
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
        return redirect()->route('admin.page-builder.index')->with('status', 'Page updated successfully.');
    }

    public function togglePublication(Request $request, CmsPage $page): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('cms.publish'), 403, 'Publishing pages requires publishing permission.');
        $page->update(['is_published' => !$page->is_published]);
        return redirect()->route('admin.page-builder.index')->with('status', $page->is_published ? 'Page published successfully.' : 'Page unpublished successfully.');
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
        return redirect()->route('admin.page-builder.edit', $copy)->with('status', 'Draft copy created.');
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $data = $request->validate(['media' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov', 'max:' . $this->maxUploadKb()]]);
        $file = $data['media'];
        $path = $file->store('site-content/media', 'public');
        return response()->json(['url' => Storage::disk('public')->url($path), 'mime' => $file->getMimeType(), 'name' => $file->getClientOriginalName()]);
    }

    private function validatePage(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'template' => ['nullable', 'string', 'max:80'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
            'builder_blocks' => ['nullable', 'string'],
        ]);

        $blocks = $request->input('builder_blocks');
        if (is_string($blocks) && trim($blocks) !== '') {
            $decoded = json_decode($blocks, true);
            abort_if(json_last_error() !== JSON_ERROR_NONE || !is_array($decoded), 422, 'Page Builder sections contain invalid data.');
            $data['builder_blocks'] = $decoded;
        } else {
            $data['builder_blocks'] = [];
        }

        $data['use_global_framework'] = true;
        $data['use_global_header'] = true;
        $data['use_global_footer'] = true;
        $data['is_published'] = $request->boolean('is_published');
        return $data;
    }

    private function guardPublishing(Request $request, bool $publishing): void
    {
        abort_unless(!$publishing || $request->user()->hasPermission('cms.publish'), 403, 'Publishing pages requires publishing permission.');
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        abort_if($base === '', 422, 'A valid page slug could not be generated.');
        $slug = $base;
        $counter = 2;
        while (CmsPage::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) $slug = $base . '-' . $counter++;
        return $slug;
    }

    private function maxUploadKb(): int
    {
        $mb = (int) SystemSetting::query()->where('key', 'uploads.content_media_max_mb')->value('value');
        return max(1, $mb ?: (int) config('fuelfree.upload.content_media_max_mb', 100)) * 1024;
    }
}
