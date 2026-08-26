@extends('layouts.portal')
@section('title','Management Team')
@section('content')
<section class="hero"><div><span class="eyebrow">WEBSITE SECTIONS · MANAGEMENT</span><h1>Management Team</h1><p>Manage leadership profiles, contact details, photos and visiting cards. Drag profiles to set the public order.</p></div><a class="primary" href="{{ route('admin.management.create') }}"><i class="fa-solid fa-plus"></i> Add member</a></section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
<div class="toolbar"><div><strong>{{ $members->count() }}</strong><span> profiles</span></div><span class="hint"><i class="fa-solid fa-grip-vertical"></i> Drag to reorder</span></div>
<div id="member-list" class="list">
@forelse($members as $member)
<article class="member" draggable="true" data-id="{{ $member->id }}" data-edit-url="{{ route('admin.management.edit',$member) }}">
    <div class="handle" title="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></div>
    <div class="avatar">@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="">@else<i class="fa-solid fa-user-tie"></i>@endif</div>
    <div class="info">
        <div class="name">{{ $member->title }}</div>
        <div class="designation">{{ $member->designation ?: $member->excerpt }}</div>
        <div class="meta">
            <span class="status {{ $member->status }}">{{ ucfirst($member->status) }}</span>
            @if($member->phone)<span class="contact"><i class="fa-solid fa-phone"></i>{{ $member->phone }}</span>@endif
            @if($member->email)<span class="contact"><i class="fa-solid fa-envelope"></i>{{ $member->email }}</span>@endif
        </div>
    </div>
    <div class="actions"><form method="POST" action="{{ route('admin.management.destroy',$member) }}" onsubmit="return confirm('Delete this management profile?')">@csrf @method('DELETE')<button title="Delete" aria-label="Delete"><i class="fa-solid fa-trash"></i></button></form></div>
</article>
@empty
<div class="empty"><i class="fa-solid fa-people-group"></i><h2>No profiles yet</h2><p>Add the first management member to start building the public leadership page.</p><a class="primary" href="{{ route('admin.management.create') }}">Add member</a></div>
@endforelse
</div>
@endsection
@push('styles')<style>
.hero{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:20px}.eyebrow{font-size:9px;letter-spacing:.16em;color:#54cde8}.hero h1{margin:6px 0;font-size:clamp(27px,4vw,42px)}.hero p{margin:0;color:#7898a5;font-size:11px;max-width:720px;line-height:1.6}.primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:11px;padding:11px 15px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;text-decoration:none;font-size:10px;font-weight:800;white-space:nowrap}.notice{padding:11px 13px;margin-bottom:12px;border-radius:11px;background:rgba(67,194,137,.1);color:#a8e5ca;font-size:10px}.toolbar{display:flex;justify-content:space-between;align-items:center;padding:13px 15px;margin-bottom:10px;border:1px solid var(--line);border-radius:14px;background:rgba(67,194,229,.035);color:#7f9ba5;font-size:10px}.toolbar strong{font-size:16px;color:#eaf8fb}.hint{color:#6f8e98}.list{display:grid;gap:8px}.member{display:grid;grid-template-columns:26px 54px minmax(0,1fr) 42px;align-items:center;gap:12px;padding:10px 12px;border:1px solid var(--line);border-radius:15px;background:linear-gradient(90deg,rgba(8,34,46,.9),rgba(4,24,33,.92));transition:transform .18s,border-color .18s,background .18s;cursor:pointer}.member:hover{border-color:rgba(78,205,232,.34);background:linear-gradient(90deg,rgba(9,40,53,.94),rgba(5,27,37,.96));transform:translateY(-1px)}.member.dragging{opacity:.45;border-color:rgba(78,205,232,.5);transform:none}.handle{color:#537883;text-align:center;cursor:grab}.handle:active{cursor:grabbing}.handle i{cursor:grab}.avatar{width:54px;height:54px;border-radius:13px;overflow:hidden;background:#092633;display:grid;place-items:center;color:#4ec7e2}.avatar img{width:100%;height:100%;object-fit:cover}.info{min-width:0}.name{font-size:13px;font-weight:800;color:#e7f6f8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.designation{font-size:10px;color:#58c6df;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.meta{display:flex;align-items:center;flex-wrap:wrap;gap:9px;margin-top:7px}.status{font-size:8px;padding:6px 8px;border-radius:999px}.status.published{color:#9ee7ca;background:rgba(49,191,139,.09);border:1px solid rgba(49,191,139,.15)}.status.draft{color:#f2c78d;background:rgba(220,153,68,.09);border:1px solid rgba(220,153,68,.15)}.contact{display:inline-flex;align-items:center;gap:5px;color:#7898a5;font-size:9px}.contact i{color:#56cce7}.actions{display:flex;align-items:center;justify-content:center}.actions form{margin:0}.actions button{width:34px;height:34px;border:1px solid transparent;border-radius:9px;background:transparent;color:#7797a2;display:grid;place-items:center;cursor:pointer}.actions button:hover{background:rgba(231,83,91,.1);color:#ff9da4;border-color:rgba(231,83,91,.18)}.empty{text-align:center;padding:55px 20px;border:1px dashed var(--line);border-radius:18px;color:#7898a5}.empty i{font-size:34px;color:#4fc8e4}.empty h2{color:#dff4f7;margin:12px 0 5px;font-size:18px}.empty p{font-size:10px;margin:0 0 16px}@media(max-width:720px){.hero{align-items:flex-start;flex-direction:column}.primary{width:100%}.member{grid-template-columns:24px 48px minmax(0,1fr) 40px;gap:10px;padding:11px 10px}.avatar{width:48px;height:48px}.contact{width:100%}.actions{justify-content:flex-end}}@media(max-width:500px){.member{grid-template-columns:22px 44px minmax(0,1fr) 38px;gap:8px;padding:10px 9px}.avatar{width:44px;height:44px}.actions button{width:32px;height:32px}.name{font-size:12px}.designation{font-size:9px}.meta{gap:6px}.toolbar{align-items:flex-start;gap:8px;flex-direction:column}}
</style>@endpush
@push('scripts')<script>
(function(){
    const list=document.getElementById('member-list');
    if(!list)return;
    let dragged=null;
    let wasDragged=false;

    list.querySelectorAll('.member').forEach(card=>{
        card.addEventListener('dragstart',()=>{dragged=card;wasDragged=true;card.classList.add('dragging')});
        card.addEventListener('dragend',async()=>{
            card.classList.remove('dragging');
            const order=[...list.querySelectorAll('.member')].map(x=>x.dataset.id);
            try{
                const r=await fetch('{{ route('admin.management.reorder') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:JSON.stringify({order})});
                if(!r.ok)throw new Error();
            }catch(e){window.location.reload()}
            setTimeout(()=>{wasDragged=false},0);
            dragged=null;
        });
        card.addEventListener('dragover',e=>{
            e.preventDefault();
            if(!dragged||dragged===card)return;
            const rect=card.getBoundingClientRect();
            if(e.clientY<rect.top+rect.height/2)card.before(dragged);else card.after(dragged);
        });
        card.addEventListener('click',e=>{
            if(wasDragged||e.target.closest('form,button,.handle'))return;
            const url=card.dataset.editUrl;
            if(url)window.location.href=url;
        });
    });
})();
</script>@endpush
