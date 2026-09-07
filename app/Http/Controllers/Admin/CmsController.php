<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\SiteContentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CmsController extends Controller
{
    public function index(Request $request): View
    {
        $pageBuilder = CmsPage::query()->get()->map(function (CmsPage $page) {
            $page->content_source = 'Page Builder';
            $page->edit_url = route('admin.page-builder.edit', $page);
            $page->toggle_url = route('admin.page-builder.toggle', $page);
            $page->delete_url = route('admin.page-builder.destroy', $page);
            $page->duplicate_url = route('admin.page-builder.duplicate', $page);
            return $page;
        });

        $websitePages = SiteContentItem::query()
            ->whereIn('type', ['company', 'plants', 'future-project', 'solution'])
            ->get()
            ->map(function (SiteContentItem $page) {
                $page->content_source = 'Website Content';
                $page->edit_url = route('admin.site-content.edit', $page);
                $page->toggle_url = route('admin.site-content.page.toggle', $page);
                $page->delete_url = route('admin.site-content.destroy', $page);
                $page->duplicate_url = null;
                return $page;
            });

        $allPages = $pageBuilder->concat($websitePages)
            ->sortByDesc(fn ($page) => $page->updated_at?->timestamp ?? 0)
            ->values();

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pages = new LengthAwarePaginator(
            $allPages->forPage($currentPage, $perPage)->values(),
            $allPages->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.cms.index', ['pages' => $pages]);
    }

    public function create(): View
    {
        return view('admin.cms.form', ['page' => new CmsPage(), 'mode' => 'create']);
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
        return view('admin.cms.form', ['page' => $page, 'mode' => 'edit']);
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
        $page->is_published = ! $page->is_published;
        $page->save();

        return redirect()->route('admin.page-builder.index')->with('status', $page->is_published ? 'Page activated successfully.' : 'Page deactivated successfully.');
    }

    public function destroy(CmsPage $page): RedirectResponse
    {
        $page->delete();
        return back()->with('status', 'Page deleted successfully.');
    }

    public function duplicate(CmsPage $page): RedirectResponse
    {
        $copy = $page->replicate();
        $copy->title = Str::limit($page->title.' Copy', 180, '');
        $copy->slug = $this->uniqueSlug($page->slug.'-copy');
        $copy->is_published = false;
        $copy->use_global_framework = true;
        $copy->use_global_header = true;
        $copy->use_global_footer = true;
        $copy->save();

        return redirect()->route('admin.page-builder.edit', $copy)->with('status', 'Draft copy created.');
    }

    private function validatePage(Request $request): array
    {
        $rawBlocks = $request->input('builder_blocks');
        if (is_string($rawBlocks)) {
            $decoded = json_decode($rawBlocks, true);
            $request->merge(['builder_blocks' => is_array($decoded) ? $decoded : []]);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'template' => ['nullable', 'string', 'max:80'],
            'builder_blocks' => ['nullable', 'array'],
            'builder_blocks.*' => ['array'],
            'builder_blocks.*.type' => ['required', 'string', 'max:40'],
            'builder_blocks.*.eyebrow' => ['nullable', 'string', 'max:120'],
            'builder_blocks.*.title' => ['nullable', 'string', 'max:180'],
            'builder_blocks.*.content' => ['nullable', 'string', 'max:20000'],
            'builder_blocks.*.image' => ['nullable', 'string', 'max:2000'],
            'builder_blocks.*.image_alt' => ['nullable', 'string', 'max:255'],
            'builder_blocks.*.url' => ['nullable', 'string', 'max:2000'],
            'builder_blocks.*.button_text' => ['nullable', 'string', 'max:120'],
            'builder_blocks.*.button_url' => ['nullable', 'string', 'max:2000'],
            'builder_blocks.*.layout' => ['nullable', 'string', 'max:40'],
            'builder_blocks.*.tone' => ['nullable', 'string', 'max:30'],
            'builder_blocks.*.align' => ['nullable', 'string', 'max:30'],
            'builder_blocks.*.columns' => ['nullable', 'integer', 'between:2,4'],
            'builder_blocks.*.visible' => ['nullable', 'boolean'],
            'builder_blocks.*.items' => ['nullable', 'array', 'max:12'],
            'builder_blocks.*.items.*' => ['array'],
            'builder_blocks.*.items.*.title' => ['nullable', 'string', 'max:180'],
            'builder_blocks.*.items.*.content' => ['nullable', 'string', 'max:2000'],
            'builder_blocks.*.items.*.value' => ['nullable', 'string', 'max:80'],
            'builder_blocks.*.items.*.label' => ['nullable', 'string', 'max:180'],
            'builder_blocks.*.items.*.image' => ['nullable', 'string', 'max:2000'],
            'builder_blocks.*.items.*.url' => ['nullable', 'string', 'max:2000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        // Framework-first by design: page content inherits the live global shell.
        $data['use_global_framework'] = true;
        $data['use_global_header'] = true;
        $data['use_global_footer'] = true;
        $data['is_published'] = $request->boolean('is_published');
        $data['builder_blocks'] = $this->normalizeBlocks($data['builder_blocks'] ?? []);

        return $data;
    }

    private function normalizeBlocks(array $blocks): array
    {
        $allowedTypes = ['hero', 'rich_text', 'image', 'split', 'cards', 'stats', 'cta', 'video', 'divider'];

        return collect($blocks)
            ->filter(fn ($block) => is_array($block) && in_array($block['type'] ?? null, $allowedTypes, true))
            ->map(function (array $block): array {
                $block['visible'] = array_key_exists('visible', $block) ? (bool) $block['visible'] : true;
                $block['tone'] = in_array($block['tone'] ?? null, ['dark', 'accent', 'light'], true) ? $block['tone'] : 'dark';
                $block['align'] = in_array($block['align'] ?? null, ['left', 'center', 'right'], true) ? $block['align'] : 'left';
                if (isset($block['items']) && is_array($block['items'])) {
                    $block['items'] = array_values(array_filter($block['items'], 'is_array'));
                }
                return $block;
            })
            ->values()
            ->all();
    }

    private function guardPublishing(Request $request, bool $publishing): void
    {
        abort_unless(! $publishing || $request->user()->hasPermission('cms.publish'), 403, 'Publishing pages requires publishing permission.');
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        abort_if($base === '', 422, 'A valid page slug could not be generated.');
        $slug = $base;
        $counter = 2;
        while (CmsPage::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }
        return $slug;
    }
}
