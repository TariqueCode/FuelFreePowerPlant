<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePopup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SitePopupController extends Controller
{
    public function index(): View
    {
        $popups = SitePopup::latest()->paginate(20);
        $publishedCount = SitePopup::where('is_published', true)->count();
        return view('admin.site-popups.index', compact('popups', 'publishedCount'));
    }

    public function create(): View
    {
        return view('admin.site-popups.form', ['popup' => new SitePopup()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $popup = new SitePopup();
        $this->save($popup, $request);
        return redirect()->route('admin.site-popups.index')->with('status', 'Announcement banner created.');
    }

    public function edit(SitePopup $popup): View
    {
        return view('admin.site-popups.form', compact('popup'));
    }

    public function update(Request $request, SitePopup $popup): RedirectResponse
    {
        if ($request->boolean('toggle')) {
            abort_unless($request->user()->hasPermission('website.publish'), 403, 'Publishing highlights requires publishing permission.');
            $popup->update(['is_published' => !$popup->is_published]);
            return redirect()->route('admin.site-popups.index')->with('status', $popup->is_published ? 'Highlight activated.' : 'Highlight deactivated.');
        }
        $this->save($popup, $request);
        return redirect()->route('admin.site-popups.index')->with('status', 'Announcement banner updated.');
    }

    public function destroy(SitePopup $popup): RedirectResponse
    {
        if ($popup->image_path) Storage::disk('public')->delete($popup->image_path);
        $popup->delete();
        return back()->with('status', 'Announcement banner deleted.');
    }

    private function save(SitePopup $popup, Request $request): void
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'url', 'max:1000'],
            'display_seconds' => ['nullable', 'integer', 'min:1', 'max:3600'],
            'is_published' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $publishing = $request->boolean('is_published');
        abort_unless(! $publishing || $request->user()->hasPermission('website.publish'), 403, 'Publishing highlights requires publishing permission.');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $this->validateImageUpload($file);

            if ($popup->image_path) Storage::disk('public')->delete($popup->image_path);
            $extension = strtolower($file->getClientOriginalExtension());
            $path = 'site-popups/' . bin2hex(random_bytes(16)) . '.' . $extension;
            $stream = fopen($file->getRealPath(), 'rb');
            try {
                if (! Storage::disk('public')->put($path, $stream)) {
                    throw ValidationException::withMessages(['image' => 'The banner image could not be saved. Please try again.']);
                }
            } finally {
                if (is_resource($stream)) fclose($stream);
            }
            $data['image_path'] = $path;
        } elseif (! $popup->exists) {
            throw ValidationException::withMessages(['image' => 'Please select a valid banner image.']);
        }

        $data['is_published'] = $publishing;
        $popup->fill($data)->save();
    }

    private function validateImageUpload(mixed $file): void
    {
        if (! $file || ! $file->isValid()) {
            throw ValidationException::withMessages(['image' => 'The banner image could not be uploaded. Please try again.']);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        if (! in_array($extension, $allowedExtensions, true)) {
            throw ValidationException::withMessages(['image' => 'Please upload a JPG, JPEG, PNG, WebP, or AVIF image.']);
        }

        $imageInfo = @getimagesize($file->getRealPath());
        $allowedTypes = array_filter([
            defined('IMAGETYPE_JPEG') ? IMAGETYPE_JPEG : null,
            defined('IMAGETYPE_PNG') ? IMAGETYPE_PNG : null,
            defined('IMAGETYPE_WEBP') ? IMAGETYPE_WEBP : null,
            defined('IMAGETYPE_AVIF') ? IMAGETYPE_AVIF : null,
        ]);

        if (! $imageInfo || ! in_array($imageInfo[2] ?? null, $allowedTypes, true)) {
            throw ValidationException::withMessages(['image' => 'The selected file is not a supported image.']);
        }
    }
}
