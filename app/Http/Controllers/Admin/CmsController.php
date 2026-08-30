<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CmsController extends Controller
{
    public function index(): View
    {
        return view('admin.cms.index', ['pages' => CmsPage::latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.cms.form', ['page' => new CmsPage(), 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePage($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        CmsPage::create($data);
        return redirect()->route('admin.cms.index')->with('status', 'CMS page created successfully.');
    }

    public function edit(CmsPage $page): View
    {
        return view('admin.cms.form', ['page' => $page, 'mode' => 'edit']);
    }

    public function update(Request $request, CmsPage $page): RedirectResponse
    {
        $data = $this->validatePage($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $page->id);
        $page->update($data);
        return redirect()->route('admin.cms.index')->with('status', 'CMS page updated successfully.');
    }

    public function destroy(CmsPage $page): RedirectResponse
    {
        $page->delete();
        return back()->with('status', 'CMS page deleted successfully.');
    }

    private function validatePage(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
        ]) + ['is_published' => $request->boolean('is_published')];
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
