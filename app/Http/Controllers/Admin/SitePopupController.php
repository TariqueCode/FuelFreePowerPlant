<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePopup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        return view('admin.site-popups.form', ['popup'=>new SitePopup()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $popup = new SitePopup();
        $this->save($popup, $request);
        return redirect()->route('admin.site-popups.index')->with('status','Announcement banner created.');
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
        return redirect()->route('admin.site-popups.index')->with('status','Announcement banner updated.');
    }

    public function destroy(SitePopup $popup): RedirectResponse
    {
        if ($popup->image_path) Storage::disk('public')->delete($popup->image_path);
        $popup->delete();
        return back()->with('status','Announcement banner deleted.');
    }

    private function save(SitePopup $popup, Request $request): void
    {
        $data=$request->validate([
            'title'=>['nullable','string','max:255'],
            'image'=>[$popup->exists?'nullable':'required','image','mimes:jpg,jpeg,png,webp,avif','max:'.$this->maxUploadKb()],
            'link_url'=>['nullable','url','max:1000'],
            'display_seconds'=>['nullable','integer','min:1','max:3600'],
            'is_published'=>['nullable','boolean'],
            'starts_at'=>['nullable','date'],
            'ends_at'=>['nullable','date','after_or_equal:starts_at'],
        ]);
        if ($request->hasFile('image')) {
            if ($popup->image_path) Storage::disk('public')->delete($popup->image_path);
            $data['image_path']=$request->file('image')->store('site-popups','public');
        }
        unset($data['image']);
        $data['is_published']=$request->boolean('is_published');
        abort_unless(! $data['is_published'] || $request->user()->hasPermission('website.publish'), 403, 'Publishing highlights requires publishing permission.');
        $popup->fill($data)->save();
    }

    private function maxUploadKb(): int
    {
        $mb=(int) \App\Models\SystemSetting::query()->where('key','uploads.popups_max_mb')->value('value');
        return max(1,$mb ?: (int) config('fuelfree.upload.popups_max_mb',50))*1024;
    }
}