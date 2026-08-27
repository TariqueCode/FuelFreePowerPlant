<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocialLinkController extends Controller
{
    public function index(): View
    {
        $links = SocialLink::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.social-links.index', compact('links'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url' => ['required', 'url', 'max:500'],
            'icon' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\s-]+$/i'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        SocialLink::create([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Social media link added.');
    }

    public function update(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'url' => ['required', 'url', 'max:500'],
            'icon' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\s-]+$/i'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $socialLink->update([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Social media link updated.');
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();

        return back()->with('status', 'Social media link removed.');
    }

    public function toggle(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update(['is_active' => ! $socialLink->is_active]);

        return back()->with('status', 'Social media visibility updated.');
    }
}
