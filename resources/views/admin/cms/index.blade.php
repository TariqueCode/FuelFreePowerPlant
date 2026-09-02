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
                    <span class="switch-knob"></span>
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
.content-pages-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.content-page-card{position:relative;display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:10px;min-width:0;padding:13px;border:1px solid var(--line);border-radius:16px;background:linear-gradient(145deg,rgba(8,38,52,.9),rgba(3,21,30,.92));transition:border-color .18s,background .18s,transform .18s}
.content-page-card:hover{border-color:rgba(78,205,232,.36);background:linear-gradient(145deg,rgba(9,42,56,.94),rgba(4,25,34,.96));transform:translateY(-1px)}
.content-page-link{display:grid;grid-template-columns:48px minmax(0,1fr);align-items:center;gap:12px;min-width:0;color:inherit;text-decoration:none}
.content-page-icon{width:48px;height:48px;display:grid;place-items:center;border-radius:13px;background:rgba(67,194,229,.08);border:1px solid rgba(78,205,232,.12);color:#58d0e9;font-size:17px}
.content-page-info{min-width:0}
.content-page-title{display:block;font-size:13px;font-weight:800;color:#e7f6f8;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.content-page-meta{display:flex;align-items:center;gap:7px;min-width:0;margin-top:7px;flex-wrap:wrap}
.content-page-source,.content-page-slug{font-size:9px;color:#7797a3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.content-page-source{color:#80cfe0}
.content-page-dot{width:3px;height:3px;border-radius:50%;background:#3b6977;flex:0 0 auto}
.content-page-actions{display:flex;align-items:center;justify-content:flex-end;gap:6px}
.content-page-actions form{margin:0}
.page-switch{width:42px;height:26px;padding:2px;border:1px solid rgba(89,201,220,.2);border-radius:999px;background:rgba(120,151,162,.12);cursor:pointer;display:flex;align-items:center}
.page-switch .switch-knob{width:20px;height:20px;border-radius:50%;background:#7895a0;transition:transform .18s,background .18s}
.page-switch.on{background:rgba(49,191,139,.18);border-color:rgba(49,191,139,.3)}
.page-switch.on .switch-knob{background:#62d6ad;transform:translateX(16px)}
.page-switch.off .switch-knob{transform:translateX(0)}
.page-delete{width:34px;height:34px;border:1px solid transparent;border-radius:9px;background:transparent;color:#7797a2;display:grid;place-items:center;cursor:pointer}
.page-delete:hover{background:rgba(231,83,91,.1);color:#ff9da4;border-color:rgba(231,83,91,.18)}
.content-page-card:focus-within{border-color:rgba(78,205,232,.45);box-shadow:0 0 0 2px rgba(78,205,232,.07)}
.notice{padding:11px 13px;margin-bottom:12px;border-radius:11px;background:rgba(67,194,137,.1);color:#a8e5ca;font-size:10px}
.empty{text-align:center;padding:55px 20px;border:1px dashed var(--line);border-radius:18px;color:#7898a5;grid-column:1/-1}
.empty i{font-size:34px;color:#4fc8e4}
.empty strong{display:block;color:#dff4f7;margin:12px 0 5px;font-size:18px}
.empty span{font-size:10px}
.pagination{padding-top:14px}
@media(max-width:900px){.content-pages-list{grid-template-columns:1fr}}
@media(max-width:560px){
 .content-pages-toolbar{align-items:stretch;flex-direction:column}
 .new-page{width:100%}
 .content-page-card{grid-template-columns:minmax(0,1fr) auto;gap:8px;padding:10px}
 .content-page-link{grid-template-columns:40px minmax(0,1fr);gap:9px}
 .content-page-icon{width:40px;height:40px;border-radius:11px;font-size:14px}
 .content-page-title{font-size:12px}
 .content-page-meta{gap:5px;margin-top:5px}
 .content-page-source,.content-page-slug{font-size:8px}
 .content-page-actions{gap:3px}
 .page-switch{width:39px;height:25px}
 .page-switch .switch-knob{width:19px;height:19px}
 .page-switch.on .switch-knob{transform:translateX(14px)}
 .page-delete{width:31px;height:31px}
}
</style>@endpush