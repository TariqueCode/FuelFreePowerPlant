@extends('layouts.portal')
@section('title','Page Builder')
@section('content')
<div class="pbi">
<header class="pbi-header">
    <div><div class="pbi-k"><i class="fa-solid fa-layer-group"></i> WEBSITE · PAGE BUILDER</div><h1>Pages</h1><p>Create structured responsive pages while inheriting the live global header, footer and framework.</p></div>
    <a class="pbi-primary" href="{{ route('admin.cms.create') }}"><i class="fa-solid fa-plus"></i><span>New Page</span></a>
</header>
@if(session('status'))<div class="pbi-alert"><i class="fa-solid fa-circle-check"></i>{{ session('status') }}</div>@endif
<div class="pbi-global"><i class="fa-solid fa-globe"></i><div><strong>Global framework is active</strong><span>Every Page Builder page uses the site's live navigation, branding, typography, theme and footer.</span></div><b>HEADER · THEME · FOOTER</b></div>
@if($pages->count())
<div class="pbi-list">
@foreach($pages as $page)
<article class="pbi-card">
    <div class="pbi-drag"><span class="pbi-grip"><i class="fa-solid fa-grip-vertical"></i></span></div>
    <div class="pbi-icon"><i class="fa-solid fa-file-lines"></i></div>
    <div class="pbi-body">
        <div class="pbi-title"><h2>{{ $page->title }}</h2><span class="pbi-live">{{ (($page->is_published??false)||(($page->status??'')==='published'))?'LIVE':'DRAFT' }}</span></div>
        <p>{{ \Illuminate\Support\Str::limit(strip_tags($page->excerpt??$page->content??''),140) ?: 'No description added yet.' }}</p>
        <div class="pbi-meta"><span><i class="fa-solid fa-database"></i>{{ $page->content_source }}</span><span><i class="fa-solid fa-link"></i>/pages/{{ $page->slug }}</span><span><i class="fa-regular fa-clock"></i>{{ optional($page->updated_at)->diffForHumans() }}</span></div>
    </div>
    <div class="pbi-actions">
        <a href="{{ $page->edit_url }}" title="Edit page"><i class="fa-solid fa-pen"></i><span>Edit</span></a>
        @if($page instanceof \App\Models\CmsPage)
        <a href="{{ route('cms.page',$page->slug) }}" target="_blank" rel="noopener" title="Preview page"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Preview</span></a>
        <form method="POST" action="{{ $page->duplicate_url }}">@csrf<button type="submit" title="Duplicate page"><i class="fa-regular fa-copy"></i><span>Duplicate</span></button></form>
        @endif
        <form method="POST" action="{{ $page->toggle_url }}">@csrf @method('PATCH')<button type="submit" title="{{ (($page->is_published??false)||(($page->status??'')==='published'))?'Unpublish':'Publish' }}"><i class="fa-solid fa-eye"></i><span>{{ (($page->is_published??false)||(($page->status??'')==='published'))?'Unpublish':'Publish' }}</span></button></form>
        <form method="POST" action="{{ $page->delete_url }}" onsubmit="return confirm('Delete this page? This cannot be undone.');">@csrf @method('DELETE')<button class="danger" type="submit" title="Delete page"><i class="fa-regular fa-trash-can"></i><span>Delete</span></button></form>
    </div>
</article>
@endforeach
</div>
@if($pages->hasPages())<div class="pbi-pages">{{ $pages->links() }}</div>@endif
@else
<div class="pbi-empty"><i class="fa-solid fa-cubes"></i><h2>Your Page Builder is ready</h2><p>The previous Page Builder catalogue is clear. Start fresh with Hero, Rich Text, Image, Split, Cards, Stats, CTA, Video and Divider sections.</p><a class="pbi-primary" href="{{ route('admin.cms.create') }}"><i class="fa-solid fa-wand-magic-sparkles"></i><span>Build First Page</span></a></div>
@endif
</div>
@endsection
@push('styles')
<style>
.pbi{max-width:1400px;margin:auto;color:#eaf8fb}.pbi-header{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:19px}.pbi-k{font-size:8px;font-weight:900;letter-spacing:.16em;color:#52d7ee}.pbi h1{margin:7px 0 6px;font-size:36px;line-height:1;letter-spacing:-.04em}.pbi-header p{margin:0;color:#7897a3;font-size:10px;line-height:1.6}.pbi-primary{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:40px;padding:0 14px;border-radius:10px;background:linear-gradient(135deg,#28b6d6,#168da8);color:#fff;text-decoration:none;font-size:9px;font-weight:800;white-space:nowrap}.pbi-primary:hover{filter:brightness(1.06);transform:translateY(-1px)}.pbi-alert{display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid rgba(66,201,155,.18);border-radius:10px;background:rgba(66,201,155,.07);color:#9ce5c9;font-size:9px;margin-bottom:12px}.pbi-global{display:flex;align-items:center;gap:11px;padding:12px 14px;margin-bottom:10px;border:1px solid rgba(73,213,239,.15);border-radius:12px;background:linear-gradient(135deg,rgba(73,213,239,.06),rgba(6,25,34,.96))}.pbi-global>i{width:34px;height:34px;display:grid;place-items:center;flex:0 0 34px;border-radius:9px;background:rgba(73,213,239,.1);color:#62dcef}.pbi-global strong,.pbi-global span{display:block}.pbi-global strong{font-size:9px}.pbi-global span{margin-top:3px;color:#708e99;font-size:7px}.pbi-global b{margin-left:auto;color:#73ddb8;font-size:6px;letter-spacing:.08em}.pbi-list{padding:0 0 4px}.pbi-card{display:grid;grid-template-columns:45px 52px minmax(0,1fr) auto;align-items:center;gap:12px;min-height:104px;margin:7px 0;padding:10px 12px;border:1px solid rgba(103,208,234,.12);border-radius:14px;background:linear-gradient(145deg,#091e29,#06151e);transition:border-color .2s,background .2s,box-shadow .2s,transform .2s}.pbi-card:hover{border-color:rgba(103,208,234,.25);background:linear-gradient(145deg,#0b2430,#071821);box-shadow:0 12px 30px rgba(0,0,0,.08);transform:translateY(-1px)}.pbi-drag{height:76px;display:grid;place-items:center}.pbi-grip{display:grid;place-items:center;width:30px;height:30px;border-radius:8px;color:#507582;cursor:grab}.pbi-grip:hover{background:rgba(73,213,239,.06);color:#9be5f1}.pbi-icon{width:52px;height:52px;display:grid;place-items:center;border-radius:13px;background:rgba(73,213,239,.08);color:#61d9ef;font-size:17px}.pbi-body{min-width:0}.pbi-title{display:flex;align-items:center;gap:8px;min-width:0}.pbi-title h2{margin:0;color:#e7f7fa;font-size:12px;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.pbi-live{flex:0 0 auto;padding:4px 7px;border-radius:999px;background:rgba(66,201,155,.08);color:#75ddb8;font-size:5.5px;font-weight:900;letter-spacing:.12em}.pbi-title+.pbi-body{}.pbi-body>p{margin:5px 0 0;color:#7897a3;font-size:8px;line-height:1.45;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.pbi-meta{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:7px;color:#5f7f8a;font-size:6.5px}.pbi-meta span{display:inline-flex;align-items:center;gap:5px;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.pbi-meta i{color:#4dbdd3;font-size:5px}.pbi-actions{display:flex;align-items:center;justify-content:flex-end;gap:5px;padding-left:9px;border-left:1px solid rgba(103,208,234,.08)}.pbi-actions a,.pbi-actions button{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:31px;padding:0 9px;border:1px solid rgba(103,208,234,.11);border-radius:8px;background:rgba(255,255,255,.018);color:#8eabb4;text-decoration:none;font-size:7px;font-weight:750;cursor:pointer;white-space:nowrap}.pbi-actions a:hover,.pbi-actions button:hover{border-color:rgba(103,208,234,.28);background:rgba(73,213,239,.055);color:#e9fbfd}.pbi-actions form{margin:0}.pbi-actions .danger{color:#d9808c}.pbi-actions .danger:hover{border-color:rgba(217,128,140,.3);background:rgba(217,128,140,.05)}.pbi-pages{margin-top:13px}.pbi-empty{text-align:center;padding:65px 20px;border:1px dashed rgba(103,208,234,.18);border-radius:15px}.pbi-empty>i{width:58px;height:58px;display:grid;place-items:center;margin:auto;border-radius:16px;background:rgba(73,213,239,.07);color:#4ed5ed;font-size:22px}.pbi-empty h2{font-size:18px;margin:14px 0 5px}.pbi-empty p{max-width:560px;margin:0 auto 16px;color:#718e99;font-size:9px;line-height:1.6}
@media(max-width:1050px){.pbi-card{grid-template-columns:40px 48px minmax(0,1fr) auto;gap:9px}.pbi-icon{width:48px;height:48px}.pbi-actions{gap:4px}.pbi-actions a,.pbi-actions button{padding:0 8px}.pbi-actions span{display:none}}
@media(max-width:720px){.pbi-header{align-items:flex-start;flex-direction:column}.pbi-primary{width:100%}.pbi-global{align-items:flex-start;flex-wrap:wrap}.pbi-global b{width:100%;margin-left:45px}.pbi-card{grid-template-columns:30px 43px minmax(0,1fr);gap:8px;min-height:0;padding:10px}.pbi-drag{height:60px}.pbi-grip{width:27px;height:27px}.pbi-icon{width:43px;height:43px;border-radius:11px;font-size:14px}.pbi-title h2{font-size:10px}.pbi-body>p{white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;font-size:7px}.pbi-meta{gap:7px;font-size:5.8px}.pbi-actions{grid-column:1/-1;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));padding:8px 0 0;border-left:0;border-top:1px solid rgba(103,208,234,.08);gap:5px}.pbi-actions a,.pbi-actions button{min-height:32px;padding:0 5px;font-size:7px}.pbi-actions span{display:inline}.pbi-empty{padding:45px 15px}}
@media(max-width:430px){.pbi h1{font-size:31px}.pbi-header p{font-size:8px}.pbi-card{grid-template-columns:28px 39px minmax(0,1fr);gap:7px;padding:9px}.pbi-icon{width:39px;height:39px;font-size:13px}.pbi-title h2{font-size:9px}.pbi-live{font-size:5px;padding:3px 6px}.pbi-meta{font-size:5.3px}.pbi-actions{grid-template-columns:repeat(4,minmax(0,1fr));gap:4px}.pbi-actions a,.pbi-actions button{min-height:30px;font-size:6.5px}}
@media(prefers-reduced-motion:reduce){.pbi-card,.pbi-primary{transition:none}}
</style>
@endpush
