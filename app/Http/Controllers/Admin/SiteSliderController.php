<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSlider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSliderController extends Controller
{
    public function index(): View
    {
        $sliders = SiteSlider::orderBy('sort_order')->orderByDesc('id')->paginate(20);
        $publishedCount = SiteSlider::where('is_published', true)->count();

        return view('admin.sliders.index', compact('sliders', 'publishedCount'));
    }

    public function create(): View
    {
        return view('admin.sliders.form', ['slider' => new SiteSlider(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $slider = new SiteSlider();
        $this->save($slider, $request);
        return redirect()->route('admin.sliders.index')->with('status', 'Slider image added successfully.');
    }

    public function edit(SiteSlider $slider): View
    {
        return view('admin.sliders.form', compact('slider'));
    }

    public function update(Request $request, SiteSlider $slider): RedirectResponse
    {
        if ($request->boolean('toggle')) {
            $slider->update(['is_published' => !$slider->is_published]);
            return redirect()->route('admin.sliders.index')->with('status', $slider->is_published ? 'Slider activated.' : 'Slider deactivated.');
        }

        $this->save($slider, $request);
        return redirect()->route('admin.sliders.index')->with('status', 'Slider image updated successfully.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['integer', 'distinct'],
        ]);

        $sliders = SiteSlider::query()->whereIn('id', $data['order'])->get()->keyBy('id');

        foreach ($data['order'] as $position => $id) {
            if (isset($sliders[$id])) {
                $sliders[$id]->update(['sort_order' => $position + 1]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(SiteSlider $slider): RedirectResponse
    {
        if ($slider->image_path) {
            Storage::disk('public')->delete($slider->image_path);
        }
        $slider->delete();
        return back()->with('status', 'Slider image deleted.');
    }

    private function save(SiteSlider $slider, Request $request): void
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'image' => [$slider->exists ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
            'link_url' => ['nullable', 'url', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        if ($request->hasFile('image')) {
            if ($slider->image_path) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $data['image_path'] = $request->file('image')->store('site-sliders', 'public');
        }

        unset($data['image']);
        $data['is_published'] = $request->boolean('is_published');

        if (!$slider->exists) {
            $data['sort_order'] = ((int) SiteSlider::query()->max('sort_order')) + 1;
        }

        $slider->fill($data)->save();
    }
}
