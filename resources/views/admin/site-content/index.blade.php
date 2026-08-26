@extends('layouts.portal')
@section('title',$title)
@section('content')
<section class="hero"><div><span class="eyebrow">CONTENT MANAGEMENT</span><h1>{{ $title }}</h1><p>{{ $type==='company' ? 'Manage all company pages in one place. Drag enabled pages to set the public menu order.' : ($type==='news' ? 'Manage news articles and notices separately from company information and management.' : 'Manage structured public website content.') }}</p></div><a class="primary" href="{{ route('admin.site-content.create', $type ? ['type'=>$type] : []) }}"><i class="fa-solid fa-plus"></i> {{ $type==='company'?'Add company content':($type==='news'?'New news / notice':'New content') }}</a></section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if(!$type)<div class="workspace"><a href="{{ route('admin.site-content.index',['type'=>'company']) }}"><i class="fa-solid fa-building"></i><strong>Company CMS</strong><span>About Us and corporate information</span></a><a href="{{ route('admin.site-content.index',['type'=>'news']) }}"><i class="fa-solid fa-newspaper"></i><strong>News & Notices CMS</strong><span>News, notices and announcements</span></a></div>@else<div class="back-row"><a href="{{ route('admin.site-content.index') }}"><i class="fa-solid fa-arrow-left"></i> All content</a><span>{{ $labels[$type] ?? ucfirst($type) }}</span></div>@endif
@if($type==='company')
<div class="toolbar"><div><strong>{{ $items->total() }}</strong><span> company pages</span></div><span class="hint"><i class="fa-solid fa-grip-vertical"></i> Drag enabled pages to reorder</span></div>
@endif
<div class="content-list {{ $type==='company' ? 'company-list' : '' }}">
@forelse($items as $item)
<article class="content-card {{ $type==='company' ? 'company-card' : '' }} {{ $item->show_in_navigation ? 'nav-enabled' : '' }}" data-edit-url="{{ route('admin.site-content.edit',$item) }}" @if($type==='company' && $item->show_in_navigation) draggable="true" data-id="{{ $item->id }}" @endif>
    @if($type==='company')<div class="handle" title="Drag to reorder">@if($item->show_in_navigation)<i class="fa-solid fa-grip-vertical"></i>@else<span>—</span>@endif</div>@endif
    <div class="content-icon"><i class="fa-regular fa-file-lines"></i></div>
    <div class="info"><div class="name">{{ $item->title }}</div><div class="slug">/{{ $item->slug }}</div><div class="meta"><span class="status {{ $item->status }}">{{ ucfirst($item->status) }}</span><span class="date">{{ $item->updated_at?->format('d M Y') }}</span></div></div>
    <div class="actions"><form method="POST" action="{{ route('admin.site-content.destroy',$item) }}" onsubmit="return confirm('Delete this content?')">@csrf @method('DELETE')<button type="submit" title="Delete" aria-label="Delete"><i class="fa-solid fa-trash"></i></button></form></div>
</article>
@empty<div class="empty"><i class="fa-regular fa-file-lines"></i><strong>No content yet</strong><span>Use the button above to create the first entry.</span></div>@endforelse
</div>
@if($items->hasPages()){{ $items->links() }}@endif
@endsection
@push('styles')<style>
.hero{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:20px}.eyebrow{font-size:9px;letter-spacing:.16em;color:#54cde8}.hero h1{margin:6px 0;font-size:clamp(27px,4vw,42px)}.hero p{margin:0;color:#7898a5;font-size:11px;max-width:720px;line-height:1.6}.primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:11px;padding:11px 15px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;text-decoration:none;font-size:10px;font-weight:800;white-space:nowrap}.workspace{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}.workspace a{padding:22px;border:1px solid var(--line);border-radius:17px;background:linear-gradient(145deg,rgba(8,38,52,.85),rgba(3,21,30,.8));display:grid;grid-template-columns:38px 1fr;column-gap:12px;align-items:center;text-decoration:none}.workspace i{grid-row:1/3;width:38px;height:38px;display:grid;place-items:center;border-radius:11px;background:rgba(67,194,229,.08);color:#61d5ed}.workspace strong{font-size:12px;color:#e4f5f8}.workspace span{font-size:9px;color:#6e8d98;margin-top:3px}.back-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:13px;color:#668793;font-size:9px}.back-row a{color:#9bc0ca;text-decoration:none}.toolbar{display:flex;justify-content:space-between;align-items:center;padding:13px 15px;margin-bottom:10px;border:1px solid var(--line);border-radius:14px;background:rgba(67,194,229,.035);color:#7f9ba5;font-size:10px}.toolbar strong{font-size:16px;color:#eaf8fb}.hint{color:#6f8e98}.content-list{display:grid;gap:8px}.content-card{display:grid;grid-template-columns:54px minmax(0,1fr) auto;align-items:center;gap:12px;padding:10px 12px;border:1px solid var(--line);border-radius:15px;background:linear-gradient(90deg,rgba(8,34,46,.9),rgba(4,24,33,.92));transition:transform .18s,border-color .18s,background .18s;cursor:pointer}.content-card.company-card{grid-template-columns:26px 54px minmax(0,1fr) auto}.content-card:hover{border-color:rgba(78,205,232,.34);background:linear-gradient(90deg,rgba(9,40,53,.94),rgba(5,27,37,.96));transform:translateY(-1px)}.content-card.nav-enabled{cursor:grab}.content-card.nav-enabled:hover{transform:none}.content-card.dragging{opacity:.45;border-color:rgba(78,205,232,.5)}.handle{color:#537883;text-align:center;cursor:grab}.handle i{cursor:grab}.handle span{color:#314f5a}.content-icon{width:54px;height:54px;border-radius:13px;background:#092633;display:grid;place-items:center;color:#4ec7e2;font-size:20px}.info{min-width:0}.name{font-size:13px;font-weight:800;color:#e7f6f8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.slug{font-size:10px;color:#58c6df;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.meta{display:flex;align-items:center;gap:10px;margin-top:7px}.status{font-size:8px;padding:6px 8px;border-radius:999px}.status.published{color:#9ee7ca;background:rgba(49,191,139,.09);border:1px solid rgba(49,191,139,.15)}.status.draft{color:#f2c78d;background:rgba(220,153,68,.09);border:1px solid rgba(220,153,68,.15)}.date{font-size:9px;color:#71919c}.actions{display:flex;align-items:center;justify-content:center}.actions form{margin:0}.actions button{width:34px;height:34px;border:1px solid transparent;border-radius:9px;background:transparent;color:#7797a2;display:grid;place-items:center;cursor:pointer}.actions button:hover{background:rgba(231,83,91,.1);color:#ff9da4;border-color:rgba(231,83,91,.18)}.notice{padding:11px 13px;margin-bottom:12px;border-radius:11px;background:rgba(67,194,137,.1);color:#a8e5ca;font-size:10px}.empty{text-align:center;padding:55px 20px;border:1px dashed var(--line);border-radius:18px;color:#7898a5}.empty i{font-size:34px;color:#4fc8e4}.empty strong{display:block;color:#dff4f7;margin:12px 0 5px;font-size:18px}.empty span{font-size:10px}@media(max-width:720px){.hero{align-items:flex-start;flex-direction:column}.primary{width:100%}.content-card{grid-template-columns:48px minmax(0,1fr) 42px;gap:10px;padding:11px 10px}.content-card.company-card{grid-template-columns:24px 48px minmax(0,1fr) 42px}.content-icon{width:48px;height:48px}.actions{justify-content:flex-end}.meta{flex-wrap:wrap}}@media(max-width:500px){.content-card{grid-template-columns:44px minmax(0,1fr) 38px;gap:8px;padding:10px 9px}.content-card.company-card{grid-template-columns:22px 44px minmax(0,1fr) 38px}.content-icon{width:44px;height:44px;font-size:17px}.actions button{width:32px;height:32px}.name{font-size:12px}.slug{font-size:9px}.toolbar{align-items:flex-start;gap:8px;flex-direction:column}.hint{font-size:9px}}
</style>@endpush
@push('head')<meta name="csrf-token" content="{{ csrf_token() }}">@endpush
@push('scripts')<script>
(function(){
    const list=document.querySelector('.company-list');
    let dragged=null;
    let wasDragged=false;

    document.querySelectorAll('.content-card').forEach(card=>{
        card.addEventListener('dragstart',()=>{dragged=card;wasDragged=true;card.classList.add('dragging')});
        card.addEventListener('dragend',()=>{card.classList.remove('dragging');setTimeout(()=>{wasDragged=false},0)});
        card.addEventListener('click',e=>{
            if(wasDragged||e.target.closest('form,button,a,.handle'))return;
            const url=card.dataset.editUrl;
            if(url)window.location.href=url;
        });
    });

    if(!list)return;
    list.querySelectorAll('.content-card.nav-enabled').forEach(card=>{
        card.addEventListener('dragover',e=>{
            e.preventDefault();
            if(!dragged||dragged===card)return;
            const rect=card.getBoundingClientRect();
            if(e.clientY>rect.top+rect.height/2)card.after(dragged);else card.before(dragged);
        });
        card.addEventListener('dragend',async()=>{
            const ids=[...list.querySelectorAll('.content-card.nav-enabled')].map(el=>el.dataset.id);
            try{
                const res=await fetch('{{ route('admin.site-content.navigation.reorder') }}',{
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content,'Accept':'application/json'},
                    body:JSON.stringify({ids})
                });
                if(!res.ok)throw new Error();
            }catch(e){window.location.reload()}
            dragged=null;
        });
    });
})();
</script>@endpush
