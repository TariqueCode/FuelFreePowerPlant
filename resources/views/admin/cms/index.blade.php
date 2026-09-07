@extends('layouts.portal')

@section('title', 'Page Builder')

@section('content')
<div class="pb-index">
    <header class="pbi-header">
        <div><div class="pbi-kicker"><i class="fa-solid fa-layer-group"></i> WEBSITE · PAGE BUILDER</div><h1>Pages</h1><p>Create responsive pages with structured sections while inheriting the live global header, footer and theme framework.</p></div>
        <a class="pbi-primary" href="{{ route('admin.page-builder.create') }}"><i class="fa-solid fa-plus"></i> New Page</a>
    </header>

    @if(session('status'))<div class="pbi-alert success"><i class="fa-solid fa-circle-check"></i>{{ session('status') }}</div>@endif

    <section class="pbi-framework">
        <div class="pbi-framework-icon"><i class="fa-solid fa-globe"></i></div>
        <div><strong>Global framework is active</strong><span>Every Page Builder page uses the same live navigation, branding, typography, colors and footer. Change the global theme once; pages stay synchronized.</span></div>
        <div class="pbi-framework-tags"><b><i class="fa-solid fa-check"></i> Header</b><b><i class="fa-solid fa-check"></i> Footer</b><b><i class="fa-solid fa-check"></i> Theme</b></div>
    </section>

    @if($pages->count())
        <section class="pbi-grid">
            @foreach($pages as $page)
                @php $isCms = $page instanceof \App\Models\CmsPage; @endphp
                <article class="pbi-card">
                    <div class="pbi-card-top"><span class="pbi-source {{ $isCms ? 'builder' : 'content' }}"><i class="fa-solid {{ $isCms ? 'fa-layer-group' : 'fa-globe' }}"></i>{{ $page->content_source }}</span><span class="pbi-status {{ ($page->is_published ?? false) || ($page->status ?? '') === 'published' ? 'published' : 'draft' }}">{{ ($page->is_published ?? false) || ($page->status ?? '') === 'published' ? 'Published' : 'Draft' }}</span></div>
                    <h2>{{ $page->title }}</h2>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($page->excerpt ?? $page->content ?? ''), 150) ?: 'No description added yet.' }}</p>
                    <div class="pbi-meta"><span><i class="fa-solid fa-link"></i>/pages/{{ $page->slug }}</span><span><i class="fa-regular fa-clock"></i>{{ optional($page->updated_at)->diffForHumans() }}</span></div>
                    <div class="pbi-actions">
                        <a href="{{ $page->edit_url }}"><i class="fa-solid fa-pen"></i>Edit</a>
                        @if($isCms)<a href="{{ route('pages.show', $page->slug) }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i>Preview</a>@endif
                        @if($isCms)<form method="POST" action="{{ $page->toggle_url }}">@csrf @method('PATCH')<button type="submit"><i class="fa-solid {{ $page->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>{{ $page->is_published ? 'Unpublish' : 'Publish' }}</button></form>@endif
                        <form method="POST" action="{{ $page->delete_url }}" onsubmit="return confirm('Delete this page? This cannot be undone.');">@csrf @method('DELETE')<button class="danger" type="submit"><i class="fa-regular fa-trash-can"></i>Delete</button></form>
                    </div>
                </article>
            @endforeach
        </section>
        @if($pages->hasPages())<div class="pbi-pagination">{{ $pages->links() }}</div>@endif
    @else
        <section class="pbi-empty"><div class="pbi-empty-icon"><i class="fa-solid fa-cubes"></i></div><h2>Your new Page Builder is ready</h2><p>The legacy Page Builder catalogue has been cleared. Start fresh with reusable Hero, Rich Text, Image, Split, Cards, Stats, CTA, Video and Divider sections.</p><a class="pbi-primary" href="{{ route('admin.page-builder.create') }}"><i class="fa-solid fa-wand-magic-sparkles"></i> Build Your First Page</a></section>
    @endif
</div>
@endsection

@push('styles')
<style>
.pb-index{max-width:1400px;margin:0 auto;padding:4px 0 48px;color:#ecf9fb}.pbi-header{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;padding:8px 0 20px}.pbi-kicker{font-size:9px;letter-spacing:.15em;font-weight:900;color:#49d5ef}.pbi-header h1{margin:5px 0 6px;font-size:36px;letter-spacing:-.8px}.pbi-header p{margin:0;color:#7897a3;font-size:11px;max-width:700px;line-height:1.65}.pbi-primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:42px;padding:0 15px;border-radius:11px;background:linear-gradient(135deg,#28b6d6,#168da8);border:1px solid rgba(73,213,239,.3);color:#fff;text-decoration:none;font-size:11px;font-weight:850;box-shadow:0 9px 25px rgba(17,151,183,.16)}.pbi-alert{display:flex;align-items:center;gap:9px;padding:11px 13px;border-radius:10px;margin-bottom:14px;font-size:10px}.pbi-alert.success{color:#9ce5c9;background:rgba(66,201,155,.07);border:1px solid rgba(66,201,155,.16)}.pbi-framework{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:12px;padding:14px 16px;border-radius:14px;border:1px solid rgba(73,213,239,.15);background:linear-gradient(135deg,rgba(73,213,239,.07),rgba(6,25,34,.96));margin-bottom:16px}.pbi-framework-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:11px;background:rgba(73,213,239,.1);color:#62dcef}.pbi-framework strong,.pbi-framework span{display:block}.pbi-framework strong{font-size:11px}.pbi-framework span{margin-top:3px;color:#708e99;font-size:9px;line-height:1.45}.pbi-framework-tags{display:flex;gap:5px;flex-wrap:wrap}.pbi-framework-tags b{padding:6px 8px;border-radius:8px;background:rgba(66,201,155,.06);border:1px solid rgba(66,201,155,.13);color:#79dcb8;font-size:8px}.pbi-framework-tags i{margin-right:4px}.pbi-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.pbi-card{padding:15px;border:1px solid rgba(103,208,234,.12);border-radius:14px;background:linear-gradient(145deg,#091e29,#06151e);min-width:0}.pbi-card-top{display:flex;align-items:center;justify-content:space-between;gap:8px}.pbi-source,.pbi-status{font-size:7px;font-weight:850;text-transform:uppercase;letter-spacing:.06em;border-radius:999px;padding:5px 7px}.pbi-source.builder{color:#74dff0;background:rgba(73,213,239,.07);border:1px solid rgba(73,213,239,.12)}.pbi-source.content{color:#b5c9cf;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.08)}.pbi-status.published{color:#75ddb8;background:rgba(66,201,155,.06);border:1px solid rgba(66,201,155,.13)}.pbi-status.draft{color:#e0bd79;background:rgba(224,189,121,.06);border:1px solid rgba(224,189,121,.12)}.pbi-card h2{margin:13px 0 6px;font-size:17px;line-height:1.2}.pbi-card p{height:40px;margin:0;color:#7897a3;font-size:9px;line-height:1.55;overflow:hidden}.pbi-meta{display:flex;justify-content:space-between;gap:10px;margin-top:13px;padding-top:10px;border-top:1px solid rgba(103,208,234,.08);color:#587682;font-size:8px}.pbi-meta span{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.pbi-actions{display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-top:12px}.pbi-actions a,.pbi-actions button{display:inline-flex;align-items:center;justify-content:center;gap:5px;min-height:30px;padding:0 8px;border-radius:8px;border:1px solid rgba(103,208,234,.1);background:rgba(255,255,255,.018);color:#8faeb7;text-decoration:none;font-size:8px;cursor:pointer}.pbi-actions a:hover,.pbi-actions button:hover{border-color:rgba(73,213,239,.25);color:#d6f2f7}.pbi-actions .danger{color:#d6818e}.pbi-actions form{margin:0}.pbi-empty{display:grid;justify-items:center;text-align:center;padding:70px 20px;border:1px dashed rgba(103,208,234,.18);border-radius:16px;background:linear-gradient(145deg,rgba(9,31,43,.6),rgba(5,19,28,.7))}.pbi-empty-icon{width:62px;height:62px;display:grid;place-items:center;border-radius:17px;background:rgba(73,213,239,.07);border:1px solid rgba(73,213,239,.15);color:#49d5ef;font-size:22px}.pbi-empty h2{margin:15px 0 6px;font-size:19px}.pbi-empty p{max-width:570px;margin:0 0 18px;color:#718e99;font-size:10px;line-height:1.65}.pbi-pagination{padding-top:16px}.pbi-pagination nav{color:#7897a3}.pbi-pagination a,.pbi-pagination span{font-size:9px!important}
@media(max-width:1050px){.pbi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.pbi-framework{grid-template-columns:auto minmax(0,1fr)}.pbi-framework-tags{grid-column:2}.pbi-framework-tags b{flex:1;text-align:center}}
@media(max-width:650px){.pbi-header{align-items:flex-start;flex-direction:column}.pbi-header h1{font-size:29px}.pbi-primary{width:100%}.pbi-framework{grid-template-columns:auto minmax(0,1fr)}.pbi-framework-tags{grid-column:1/-1}.pbi-grid{grid-template-columns:1fr}.pbi-card p{height:auto;max-height:44px}.pbi-actions a,.pbi-actions button{flex:1;min-width:70px}.pbi-empty{padding:52px 15px}}
</style>
@endpush
