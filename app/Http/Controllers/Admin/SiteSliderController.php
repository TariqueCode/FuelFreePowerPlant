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
            abort_unless($request->user()->hasPermission('website.publish'), 403, 'Publishing sliders requires publishing permission.');
            $slider->update(['is_published' => !$slider->is_published]);
            return redirect()->route('admin.sliders.index')->with(
                'status',
                $slider->is_published ? 'Slider activated.' : 'Slider deactivated.'
            );
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
        $publishing = $request->boolean('is_published');
        abort_unless(! $publishing || $request->user()->hasPermission('website.publish'), 403, 'Publishing sliders requires publishing permission.');

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'image' => [$slider->exists ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:'.$this->maxUploadKb()],
            'link_url' => ['nullable', 'url', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ], [
            'image.required' => 'Please choose a slider image before saving.',
            'image.file' => 'The selected image could not be uploaded. Please choose it again.',
            'image.mimes' => 'Slider image must be JPG, JPEG, PNG or WebP.',
            'image.max' => 'Slider image exceeds the upload limit configured in Admin Settings.',
            'link_url.url' => 'Destination URL must be a valid URL, for example https://example.com.',
            'ends_at.after_or_equal' => 'End time must be after or equal to the start time.',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if (!$file->isValid()) {
                throw new \RuntimeException('The image upload failed. Please choose the image again.');
            }

            $path = $file->store('site-sliders', 'public');

            if (!$path) {
                throw new \RuntimeException('The server could not save the image.');
            }

            if ($slider->image_path) {
                Storage::disk('public')->delete($slider->image_path);
            }

            $data['image_path'] = $path;
        }

        unset($data['image']);
        $data['is_published'] = $publishing;

        if (!$slider->exists) {
            $data['sort_order'] = ((int) SiteSlider::query()->max('sort_order')) + 1;
        }

        $slider->fill($data)->save();
    }

    private function maxUploadKb(): int
    {
        $mb = (int) \App\Models\SystemSetting::query()->where('key', 'uploads.sliders_max_mb')->value('value');
        return max(1, $mb ?: (int) config('fuelfree.upload.sliders_max_mb', 50)) * 1024;
    }
}