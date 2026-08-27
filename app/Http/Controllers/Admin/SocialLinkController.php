<?php

namespace AppHttpControllersAdmin;

use AppHttpControllersController;
use AppModelsSocialLink;
use IlluminateHttpJsonResponse;
use IlluminateHttpRedirectResponse;
use IlluminateHttpRequest;
use IlluminateSupportFacadesCache;
use IlluminateViewView;

class SocialLinkController extends Controller
{
    public function index(): View
    {
        $links = SocialLink::query()->orderBy('sort_order')->orderBy('id')->get();
        $platforms = config('fuelfree.social.platforms');

        return view('admin.social-links.index', compact('links', 'platforms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateLink($request);
        $platform = config('fuelfree.social.platforms.'.$data['platform']);

        SocialLink::create([
            'platform' => $data['platform'],
            'label' => $platform['label'],
            'url' => $data['url'],
            'icon' => $platform['icon'],
            'sort_order' => $this->nextOrder(),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->clearCache();

        return back()->with('status', $platform['label'].' link added.');
    }

    public function update(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $data = $this->validateLink($request);
        $platform = config('fuelfree.social.platforms.'.$data['platform']);

        $socialLink->update([
            'platform' => $data['platform'],
            'label' => $platform['label'],
            'url' => $data['url'],
            'icon' => $platform['icon'],
            'sort_order' => $socialLink->sort_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->clearCache();

        return back()->with('status', $platform['label'].' link updated.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate(['order' => ['required','array'],'order.*' => ['integer']]);
        $links = SocialLink::query()->whereIn('id', $data['order'])->get()->keyBy('id');

        foreach ($data['order'] as $position => $id) {
            if (isset($links[$id])) {
                $links[$id]->update(['sort_order' => $position + 1]);
            }
        }

        $this->clearCache();

        return response()->json(['ok' => true]);
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();
        $this->clearCache();

        return back()->with('status', 'Social media link removed.');
    }

    public function toggle(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update(['is_active' => ! $socialLink->is_active]);
        $this->clearCache();

        return back()->with('status', 'Social media visibility updated.');
    }

    private function validateLink(Request $request): array
    {
        return $request->validate([
            'platform' => ['required','string','in:'.implode(',', array_keys(config('fuelfree.social.platforms')))],
            'url' => ['required','url','max:500'],
            'is_active' => ['nullable','boolean'],
        ]);
    }

    private function nextOrder(): int
    {
        return ((int) SocialLink::query()->max('sort_order')) + 1;
    }

    private function clearCache(): void
    {
        Cache::forget('public.social-links');
    }
}