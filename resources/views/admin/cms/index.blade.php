@extends('layouts.portal')
@section('title','Content Pages')
@section('content')
<section class="hero content-pages-intro"><div class="eyebrow">CONTENT MANAGEMENT</div><h1>Content Pages</h1><p>Manage all website pages from one list. Tap a card to open and manage its content; use the controls here only for publishing status or deletion.</p></section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
<div class="content-pages-toolbar"><div class="content-pages-count"><strong>{{ $pages->total() }}</strong> content pages</div><a class="new-page" href="{{ route('admin.cms.create') }}"><i class="fa-solid fa-plus"></i> New page</a></div>
<div class="content-pages-list">
@forelse($pages as $page)
<article class="content-page-card">
    <a class="content-page-link" href="{{ $page->edit_url }}" aria-label="Manage {{ $page->title }}">
        <span class="content-page-icon"><i class="fa-regular fa-file-lines"></i></span>
        <span class="content-page-info">
            <span class="content-page-title">{{ $page->title }}</span>
            <span class="content-page-meta">
                <span class="content-page-source">{{ $page->content_source }}</span>
                <span class="content-page-dot"></span>
                <span class="content-page-slug">{{ $page->content_source === 'Page Builder' ? '/pages/'.$page->slug : '/'.$page->slug }}</span>
            </span>
        </span>
    </a>
    <div class="content-page-actions">
        @if(($page->content_source === 'Page Builder' && auth()->user()->hasPermission('cms.publish')) || ($page->content_source === 'Website Content' && auth()->user()->hasPermission('website.publish')))
            <form method="POST" action="{{ $page->toggle_url }}">
                @csrf @method('PATCH')
                <button type="submit" class="page-switch {{ ($page->is_published || $page->status === 'published') ? 'on' : 'off' }}" title="{{ ($page->is_published || $page->status === 'published') ? 'Deactivate' : 'Activate' }}" aria-label="{{ ($page->is_published || $page->status === 'published') ? 'Deactivate' : 'Activate' }}">
                    <span class="switch-track"><span class="switch-knob"></span></span>
                </button>
            </form>
        @endif
        @if(($page->content_source === 'Page Builder' && auth()->user()->hasPermission('cms.manage')) || ($page->content_source === 'Website Content' && auth()->user()->hasPermission('website.manage')))
            <form method="POST" action="{{ $page->delete_url }}" onsubmit="return confirm('Delete this page?')">
                @csrf @method('DELETE')
                <button type="submit" class="page-delete" title="Delete" aria-label="Delete"><i class="fa-solid fa-trash-can"></i></button>
            </form>
        @endif
    </div>
</article>
@empty
<div class="empty"><i class="fa-regular fa-file-lines"></i><strong>No content pages found.</strong><span>Create a page to begin managing website content.</span></div>
@endforelse
</div>
@if($pages->hasPages())<div class="pagination">{{ $pages->links() }}</div>@endif
@endsection
@push('styles')<style>
.content-pages-intro{margin-bottom:18px}
.content-pages-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:14px}
.content-pages-count{color:#7898a5;font-size:10px}
.content-pages-count strong{color:#e5f6f9;font-size:14px;margin-right:4px}
.new-page{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:11px 15px;border-radius:11px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;text-decoration:none;font-size:10px;font-weight:800;white-space:nowrap}
.content-pages-list{display:grid;grid-template-columns:1fr;gap:12px}
.content-page-card{position:relative;display:grid;grid-template-columns:minmax(0,1fr) 88px;align-items:stretch;min-width:0;min-height:92px;overflow:hidden;border:1px solid var(--line);border-radius:16px;background:linear-gradient(145deg,rgba(8,37,50,.9),rgba(3,19,27,.94));transition:transform .18s,border-color .18s,background .18s,box-shadow .18s}
.content-page-card:hover{transform:translateY(-1px);border-color:rgba(72,216,241,.36);background:linear-gradient(145deg,rgba(9,42,56,.94),rgba(4,25,34,.96));box-shadow:0 8px 22px rgba(0,0,0,.14)}
.content-page-link{display:grid;grid-template-columns:58px minmax(0,1fr);align-items:center;gap:14px;min-width:0;padding:14px 16px;color:inherit;text-decoration:none}
.content-page-icon{width:58px;height:58px;display:grid;place-items:center;border-radius:14px;background:rgba(72,216,241,.07);border:1px solid rgba(72,216,241,.13);color:#58d0e9;font-size:19px}
.content-page-info{min-width:0}
.content-page-title{display:block;font-size:15px;font-weight:800;color:#e7f6f8;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.content-page-meta{display:flex;align-items:center;gap:8px;min-width:0;margin-top:7px;flex-wrap:wrap}
.content-page-source{display:inline-flex;align-items:center;min-height:22px;padding:4px 8px;border-radius:999px;background:rgba(72,216,241,.07);border:1px solid rgba(72,216,241,.12);color:#80cfe0;font-size:8px;font-weight:700;white-space:nowrap}
.content-page-slug{font-size:9px;color:#7797a3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.content-page-dot{width:3px;height:3px;border-radius:50%;background:#3b6977;flex:0 0 auto}
.content-page-actions{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border-left:1px solid rgba(116,221,239,.1);padding:10px}
.content-page-actions form{margin:0;width:100%;display:flex;justify-content:center}
.page-switch,.page-delete{width:40px;height:40px;box-sizing:border-box;border:1px solid;cursor:pointer;display:grid;place-items:center;border-radius:11px}
.page-switch{padding:0;background:rgba(72,216,241,.06);border-color:rgba(72,216,241,.18)}
.page-switch.on{background:rgba(67,194,137,.08);border-color:rgba(67,194,137,.24)}
.page-switch.off{background:rgba(72,216,241,.06);border-color:rgba(72,216,241,.18)}
.page-switch:hover,.page-delete:hover{transform:translateY(-1px);filter:brightness(1.08)}
.switch-track{position:relative;width:24px;height:14px;border-radius:999px;background:#405962;display:block;transition:background .18s ease}
.switch-knob{position:absolute;top:2px;left:2px;width:10px;height:10px;border-radius:50%;background:#b3c4c8;box-shadow:0 1px 3px rgba(0,0,0,.3);transition:left .18s ease,background .18s ease}
.page-switch.on .switch-track{background:#32b985}
.page-switch.on .switch-knob{left:12px;background:#effff8}
.page-delete{padding:0;background:rgba(255,93,113,.045);border-color:rgba(255,93,113,.14);color:#ff9eaa;font-size:13px}
.page-delete:hover{background:rgba(255,93,113,.11);border-color:rgba(255,93,113,.24)}
.content-page-card:focus-within{border-color:rgba(78,205,232,.45);box-shadow:0 0 0 2px rgba(78,205,232,.07)}
.notice{padding:11px 13px;margin-bottom:12px;border-radius:11px;background:rgba(67,194,137,.1);color:#a8e5ca;font-size:10px}
.empty{text-align:center;padding:55px 20px;border:1px dashed var(--line);border-radius:18px;color:#7898a5}
.empty i{font-size:34px;color:#4fc8e4}
.empty strong{display:block;color:#dff4f7;margin:12px 0 5px;font-size:18px}
.empty span{font-size:10px}
.pagination{padding-top:14px}
@media(max-width:900px){
 .content-page-link{padding:13px 14px}
 .content-page-title{font-size:14px}
}
@media(max-width:600px){
 .content-pages-intro{margin-bottom:12px}
 .content-pages-toolbar{align-items:stretch;flex-direction:column;gap:8px}
 .new-page{width:100%}
 .content-pages-list{gap:10px}
 .content-page-card{grid-template-columns:minmax(0,1fr) 70px;min-height:82px;border-radius:14px}
 .content-page-link{grid-template-columns:48px minmax(0,1fr);gap:10px;padding:10px}
 .content-page-icon{width:48px;height:48px;border-radius:12px;font-size:16px}
 .content-page-title{font-size:12px}
 .content-page-meta{gap:5px;margin-top:5px}
 .content-page-source{min-height:19px;padding:3px 6px;font-size:7px}
 .content-page-slug{font-size:8px}
 .content-page-dot{width:2px;height:2px}
 .content-page-actions{padding:6px;gap:5px}
 .page-switch,.page-delete{width:32px;height:32px;border-radius:9px}
 .switch-track{width:19px;height:11px}
 .switch-knob{width:7px;height:7px;top:2px;left:2px}
 .page-switch.on .switch-knob{left:10px}
 .page-delete{font-size:12px}
}
@media(max-width:380px){
 .content-page-card{grid-template-columns:minmax(0,1fr) 64px}
 .content-page-link{grid-template-columns:42px minmax(0,1fr);gap:8px;padding:9px}
 .content-page-icon{width:42px;height:42px;font-size:14px}
 .content-page-title{font-size:11px}
 .content-page-source{font-size:6.5px;padding:3px 5px}
 .content-page-slug{font-size:7px}
 .content-page-actions{padding:5px}
 .page-switch,.page-delete{width:30px;height:30px}
 .switch-track{width:18px;height:10px}
 .switch-knob{width:6px;height:6px}
 .page-switch.on .switch-knob{left:10px}
}
@media(hover:none){
 .content-page-card:hover{transform:none;box-shadow:none}
 .page-switch:hover,.page-delete:hover{transform:none;filter:none}
}
</style>