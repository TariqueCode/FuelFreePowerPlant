@extends('layouts.portal')
@section('title','News & Event')
@section('content')
<div class="nei-wrap">
<header class="nei-header"><div><div class="nei-eyebrow">WEBSITE · NEWS & EVENT</div><h1>News & Event</h1><p>Manage publications using the same visual structure presented to website visitors.</p></div><a class="nei-new" href="{{ route('admin.news_and_event.create') }}"><i class="fa-solid fa-plus"></i> New News / Event</a></header>
@if(session('status'))<div class="nei-alert"><i class="fa-solid fa-circle-check"></i>{{ session('status') }}</div>@endif
<form method="GET" class="nei-search"><div><i class="fa-solid fa-magnifying-glass"></i><input name="q" value="{{ $search }}" placeholder="Search news and events…"></div><button type="submit">Search</button></form>
<div class="nei-list">@forelse($items as $item)<article class="nei-card" tabindex="0" role="link" data-edit-url="{{ route('admin.news_and_event.edit',$item) }}" aria-label="Edit {{ $item->title }}">
    <div class="nei-media">@if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->cover_alt ?: $item->title }}" loading="lazy">@else<div><i class="fa-regular fa-image"></i><span>No thumbnail</span></div>@endif</div>
    <div class="nei-kind {{ $item->type==='announcement'?'event':'' }}">{{ $item->type==='announcement'?'EVENT':'NEWS' }}</div>
    <div class="nei-body"><div class="nei-top"><span>{{ $item->type==='announcement'?'Event':'News' }}</span><b class="status-badge {{ $item->status==='published'?'published':'draft' }}">{{ strtoupper($item->status) }}</b></div><h2>{{ $item->title }}</h2><p>{{ \Illuminate\Support\Str::limit(strip_tags($item->excerpt ?: $item->content ?: ''),180) ?: 'No introduction added yet.' }}</p><div class="nei-meta"><span><i class="fa-regular fa-calendar"></i>{{ $item->published_at?->format('d F Y') ?: 'Not published' }}</span>@if($item->is_featured)<span class="featured"><i class="fa-solid fa-star"></i> Featured</span>@endif</div><div class="nei-actions"><form method="POST" action="{{ route('admin.news_and_event.toggle',$item) }}">@csrf @method('PATCH')<button type="submit" class="publish-control {{ $item->status==='published'?'is-published':'is-draft' }}" aria-label="{{ $item->status==='published'?'Unpublish':'Publish' }}"><i class="fa-solid fa-{{ $item->status==='published'?'eye-slash':'eye' }}"></i><span>{{ $item->status==='published'?'Unpublish':'Publish' }}</span></button></form><form method="POST" action="{{ route('admin.news_and_event.destroy',$item) }}" onsubmit="return confirm('Delete this item?')">@csrf @method('DELETE')<button type="submit" class="delete-control danger" aria-label="Delete"><i class="fa-regular fa-trash-can"></i><span>Delete</span></button></form></div></div>
</article>@empty<div class="nei-empty"><i class="fa-regular fa-newspaper"></i><h2>No News & Event yet</h2><p>Create your first publication using the News & Event editor.</p><a href="{{ route('admin.news_and_event.create') }}">Create First Item</a></div>@endforelse</div>
@if($items->hasPages())<div class="nei-pager">{{ $items->links() }}</div>@endif
</div>
@endsection
@push('styles')
<style>
/* News & Event — global card language, responsive on every device. */
.nei-wrap{width:100%;max-width:1400px;margin:0 auto;color:#eaf8fb;min-width:0}.nei-header{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;padding:5px 0 18px}.nei-header>div{min-width:0}.nei-eyebrow{font-size:9px;font-weight:900;letter-spacing:.18em;color:#52d7ee}.nei-header h1{margin:6px 0 4px;font-size:clamp(30px,3vw,40px);letter-spacing:-.035em;line-height:1.08}.nei-header p{margin:0;color:#7897a3;font-size:11px;line-height:1.5}.nei-new{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:42px;padding:0 14px;border-radius:10px;background:linear-gradient(135deg,#28b6d6,#168da8);color:#fff;text-decoration:none;font-size:10px;font-weight:800;white-space:nowrap;flex:0 0 auto}.nei-alert{display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid rgba(66,201,155,.18);border-radius:10px;background:rgba(66,201,155,.07);color:#9ce5c9;font-size:10px;margin-bottom:12px}.nei-search{display:flex;gap:8px;margin-bottom:14px;min-width:0}.nei-search>div{min-width:0;flex:1;display:flex;align-items:center;gap:9px;min-height:42px;padding:0 12px;border:1px solid rgba(103,208,234,.12);border-radius:10px;background:#071923}.nei-search i{color:#51d8f0;flex:0 0 auto}.nei-search input{width:100%;min-width:0;border:0;outline:0;background:transparent;color:#eaf8fb;font-size:10px}.nei-search input::placeholder{color:#557580}.nei-search button{min-height:42px;padding:0 15px;border:0;border-radius:10px;background:#123746;color:#d9f6fa;font-weight:800;font-size:9px;cursor:pointer;flex:0 0 auto}.nei-list{display:grid;gap:11px;min-width:0}.nei-card{position:relative;display:grid;grid-template-columns:190px 30px minmax(0,1fr);min-height:190px;overflow:hidden;border:1px solid var(--ff-card-border,rgba(103,208,234,.12));border-radius:var(--ff-card-radius,18px);background:var(--ff-card-bg-soft,linear-gradient(145deg,#091e29,#06151e));box-shadow:var(--ff-card-shadow-raised,0 22px 70px rgba(0,0,0,.28));min-width:0;cursor:pointer;transition:transform .2s,border-color .2s,box-shadow .2s}.nei-card:hover{transform:translateY(-2px);border-color:var(--ff-card-border-hover,rgba(100,224,192,.28));box-shadow:0 14px 42px rgba(0,0,0,.22)}.nei-card:focus-visible{outline:2px solid rgba(85,217,239,.55);outline-offset:3px}.nei-media{width:190px!important;height:190px!important;min-width:190px!important;min-height:190px!important;aspect-ratio:1/1!important;background:radial-gradient(circle at 50% 50%,rgba(72,216,241,.13),transparent 52%),#061923;overflow:hidden}.nei-media img{width:100%!important;height:100%!important;max-width:none!important;max-height:none!important;min-width:100%!important;min-height:100%!important;display:block!important;object-fit:cover!important;object-position:center center!important}.nei-media>div{height:100%;display:grid;place-content:center;text-align:center;color:#4ccfe7;gap:7px}.nei-media i{font-size:26px}.nei-media span{font-size:8px;color:#668590}.nei-kind{display:flex;align-items:center;justify-content:center;border-left:1px solid rgba(72,216,241,.1);border-right:1px solid rgba(72,216,241,.1);color:#51d8f0;font-size:7px;font-weight:900;letter-spacing:.15em;writing-mode:vertical-rl;transform:rotate(180deg);min-width:0}.nei-kind.event{color:#f0c58e}.nei-body{padding:17px 19px;min-width:0;display:flex;flex-direction:column;padding-right:150px}.nei-top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px;color:#70d9ea;font-size:8px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;min-width:0}.nei-top>span{min-width:0}.nei-top .status-badge{display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:999px;font-size:8px;font-weight:900;letter-spacing:.08em;white-space:nowrap;line-height:1}.nei-top .status-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;box-shadow:0 0 10px currentColor}.nei-top .status-badge.published{color:#27e394;background:rgba(39,227,148,.12)}.nei-top .status-badge.draft{color:#f0bd61;background:rgba(240,189,97,.12)}.nei-body h2{margin:10px 0 6px;color:#edfaff;font-size:18px;line-height:1.3;letter-spacing:-.02em;overflow-wrap:anywhere;word-break:break-word}.nei-body p{margin:0;color:#7897a3;font-size:9px;line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;overflow-wrap:anywhere}.nei-meta{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:auto;padding-top:10px;color:#789aa5;font-size:8px}.nei-meta span{min-width:0;overflow-wrap:anywhere}.nei-meta i{margin-right:5px}.nei-meta .featured{color:#f0c58e}.nei-actions{position:absolute;top:0;right:0;bottom:0;width:112px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;border-left:1px solid rgba(72,216,241,.1);background:rgba(2,16,23,.22);z-index:2}.nei-actions form{margin:0;display:block}.nei-actions button{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;width:54px;height:54px;padding:0;border-radius:14px;border:1px solid rgba(103,208,234,.14);background:rgba(6,28,38,.72);color:#9cc2cb;font-size:0;cursor:pointer;white-space:nowrap;transition:transform .2s,border-color .2s,background .2s}.nei-actions button:hover{transform:translateY(-1px);border-color:rgba(100,224,192,.35)}.nei-actions button i{font-size:18px;line-height:1}.nei-actions .publish-control{position:relative;width:52px;height:30px;border-radius:999px;display:block;padding:0;background:rgba(31,66,77,.7);border-color:rgba(103,208,234,.18)}.nei-actions .publish-control i{display:none}.nei-actions .publish-control span{display:block;font-size:0}.nei-actions .publish-control::before{content:"";position:absolute;top:5px;left:5px;width:18px;height:18px;border-radius:50%;background:#b5c9ce;box-shadow:0 1px 4px rgba(0,0,0,.35);transition:left .2s,background .2s}.nei-actions .publish-control::after{content:"";position:absolute;inset:0;border-radius:999px;box-shadow:0 0 0 1px rgba(103,208,234,.04)}.nei-actions .publish-control.is-published{background:rgba(30,201,125,.22);border-color:rgba(39,227,148,.42)}.nei-actions .publish-control.is-published::before{left:27px;background:#ecfff6;box-shadow:0 0 12px rgba(39,227,148,.65)}.nei-actions .delete-control{color:#ff8292;border-color:rgba(255,105,124,.17);height:52px}.nei-actions .delete-control i{font-size:18px}.nei-actions .delete-control span{display:none}.nei-empty{padding:65px 20px;text-align:center;border:1px dashed rgba(103,208,234,.18);border-radius:15px}.nei-empty>i{font-size:28px;color:#4ed5ed}.nei-empty h2{font-size:18px;margin:14px 0 5px}.nei-empty p{color:#718e99;font-size:9px}.nei-empty a{display:inline-flex;margin-top:10px;padding:9px 13px;border-radius:9px;background:#168da8;color:#fff;text-decoration:none;font-size:9px}.nei-pager{margin-top:15px;min-width:0;overflow-x:auto;overflow-y:hidden}.nei-pager nav{min-width:max-content}
@media(max-width:1024px){.nei-wrap{max-width:none}.nei-header{align-items:flex-start}.nei-card{grid-template-columns:155px 26px minmax(0,1fr);min-height:155px}.nei-media{width:155px!important;height:155px!important;min-width:155px!important;min-height:155px!important}.nei-body{padding:14px;padding-right:125px}.nei-body h2{font-size:16px}.nei-body p{font-size:9px}.nei-actions{width:96px;gap:11px}.nei-actions .publish-control{width:48px}.nei-actions .publish-control.is-published::before{left:25px}.nei-actions .delete-control{height:48px;width:50px}}
@media(max-width:760px){.nei-header{align-items:stretch;flex-direction:column;gap:12px;padding-bottom:14px}.nei-header h1{font-size:clamp(27px,8vw,34px)}.nei-header p{font-size:10px}.nei-new{align-self:stretch;width:100%;min-height:44px}.nei-search{gap:7px}.nei-search>div{min-height:42px}.nei-search button{min-height:42px;padding:0 13px}.nei-card{grid-template-columns:110px 22px minmax(0,1fr);min-height:110px}.nei-media{width:110px!important;height:110px!important;min-width:110px!important;min-height:110px!important}.nei-body{padding:11px;padding-right:92px}.nei-body h2{font-size:14px}.nei-body p{font-size:8px;-webkit-line-clamp:2}.nei-meta{font-size:7px;gap:8px}.nei-top{font-size:7px}.nei-top b{font-size:7px}.nei-top .status-badge{padding:6px 8px;font-size:7px}.nei-top .status-badge::before{width:5px;height:5px}.nei-actions{width:74px;gap:9px}.nei-actions .publish-control{width:44px;height:27px}.nei-actions .publish-control::before{top:4px;left:4px;width:17px;height:17px}.nei-actions .publish-control.is-published::before{left:23px}.nei-actions .delete-control{width:46px;height:44px}.nei-actions .delete-control i{font-size:16px}}
@media(max-width:560px){.nei-search{flex-direction:column}.nei-search button{width:100%}.nei-card{grid-template-columns:1fr;min-height:0}.nei-media{width:100%!important;height:auto!important;aspect-ratio:1/1!important;min-width:0!important;min-height:0!important;max-height:none!important}.nei-body{padding:14px;padding-right:14px;min-height:175px}.nei-top{align-items:flex-start}.nei-actions{position:absolute;top:auto;right:0;bottom:0;left:0;width:100%;height:64px;flex-direction:row;justify-content:flex-end;padding:0 14px;border-left:0;border-top:1px solid rgba(72,216,241,.1);background:rgba(2,16,23,.72)}.nei-actions .publish-control{width:48px}.nei-actions .publish-control.is-published::before{left:25px}.nei-actions .delete-control{width:48px;height:44px}.nei-body{padding-bottom:78px}.nei-empty{padding:48px 16px}}
@media(max-width:380px){.nei-media{max-height:none}.nei-body{padding:12px;padding-bottom:76px}.nei-body h2{font-size:13px}.nei-actions{height:62px;padding:0 12px;gap:8px}.nei-actions .publish-control{width:46px}.nei-actions .publish-control.is-published::before{left:24px}.nei-actions .delete-control{width:46px;height:42px}.nei-meta{font-size:7px}}
</style>
@endpush
@push('scripts')
<script>
(() => {
    document.querySelectorAll('.nei-card[data-edit-url]').forEach((card) => {
        const openEditor = () => window.location.href = card.dataset.editUrl;
        card.addEventListener('click', (event) => {
            if (event.target.closest('.nei-actions, button, form, a')) return;
            openEditor();
        });
        card.addEventListener('keydown', (event) => {
            if ((event.key === 'Enter' || event.key === ' ') && document.activeElement === card) {
                event.preventDefault();
                openEditor();
            }
        });
        card.querySelectorAll('.nei-actions, .nei-actions form, .nei-actions button').forEach((element) => {
            element.addEventListener('click', (event) => event.stopPropagation());
        });
    });
})();
</script>
@endpush

@push('styles')
<style>
/* FuelFree PowerPlant — NEWS & EVENT MOBILE LIST SYSTEM */

/*
 * Keep the exact desktop list identity on mobile.
 * Mobile only reduces dimensions; it does NOT convert cards
 * into a different stacked component.
 */

@media(max-width:560px){

    .nei-wrap{
        width:100%!important;
        max-width:none!important;
        min-width:0!important;
    }

    .nei-header{
        display:flex!important;
        flex-direction:column!important;
        align-items:stretch!important;
        gap:11px!important;
    }

    .nei-header h1{
        font-size:28px!important;
        line-height:1.08!important;
    }

    .nei-header p{
        font-size:9px!important;
        line-height:1.5!important;
    }

    .nei-new{
        width:100%!important;
        min-height:42px!important;
        flex:none!important;
    }

    .nei-search{
        display:flex!important;
        flex-direction:row!important;
        align-items:stretch!important;
        gap:6px!important;
    }

    .nei-search>div{
        flex:1 1 auto!important;
        min-width:0!important;
    }

    .nei-search button{
        width:auto!important;
        min-width:68px!important;
        flex:0 0 auto!important;
    }

    /*
     * IMPORTANT:
     * Preserve horizontal list structure on mobile.
     */
    .nei-card{
        display:grid!important;
        grid-template-columns:82px 20px minmax(0,1fr)!important;
        min-height:118px!important;
        height:auto!important;
        border-radius:15px!important;
        overflow:hidden!important;
    }

    .nei-media{
        width:82px!important;
        height:118px!important;
        min-width:82px!important;
        min-height:118px!important;
        aspect-ratio:auto!important;
    }

    .nei-media img{
        width:100%!important;
        height:100%!important;
        object-fit:cover!important;
        object-position:center!important;
    }

    .nei-kind{
        min-width:20px!important;
        width:20px!important;
        font-size:6px!important;
        letter-spacing:.12em!important;
    }

    .nei-body{
        min-width:0!important;
        min-height:118px!important;
        padding:11px 76px 11px 10px!important;
        padding-right:76px!important;
    }

    .nei-top{
        gap:5px!important;
        font-size:6px!important;
    }

    .nei-top .status-badge{
        padding:5px 7px!important;
        font-size:6px!important;
        gap:4px!important;
    }

    .nei-top .status-badge::before{
        width:4px!important;
        height:4px!important;
    }

    .nei-body h2{
        margin:7px 0 5px!important;
        font-size:13px!important;
        line-height:1.25!important;
        -webkit-line-clamp:2!important;
    }

    .nei-body p{
        font-size:7px!important;
        line-height:1.45!important;
        -webkit-line-clamp:2!important;
    }

    .nei-meta{
        gap:6px!important;
        padding-top:7px!important;
        font-size:6px!important;
        line-height:1.3!important;
    }

    /*
     * Keep actions on the right side, matching desktop list.
     */
    .nei-actions{
        position:absolute!important;
        top:0!important;
        right:0!important;
        bottom:0!important;
        left:auto!important;
        width:64px!important;
        height:100%!important;
        padding:0!important;
        border-left:1px solid rgba(72,216,241,.10)!important;
        border-top:0!important;
        flex-direction:column!important;
        justify-content:center!important;
        align-items:center!important;
        gap:9px!important;
        background:rgba(2,16,23,.32)!important;
    }

    .nei-actions .publish-control{
        width:42px!important;
        height:25px!important;
        flex:0 0 25px!important;
    }

    .nei-actions .publish-control::before{
        top:4px!important;
        left:4px!important;
        width:16px!important;
        height:16px!important;
    }

    .nei-actions .publish-control.is-published::before{
        left:22px!important;
    }

    .nei-actions .delete-control{
        width:40px!important;
        height:40px!important;
        flex:0 0 40px!important;
        border-radius:11px!important;
    }

    .nei-actions .delete-control i{
        font-size:14px!important;
    }

    .nei-actions button:hover{
        transform:none!important;
    }

    .nei-pager{
        width:100%!important;
        overflow-x:auto!important;
    }
}

/* Small phones */
@media(max-width:390px){

    .nei-card{
        grid-template-columns:74px 18px minmax(0,1fr)!important;
        min-height:108px!important;
    }

    .nei-media{
        width:74px!important;
        height:108px!important;
        min-width:74px!important;
        min-height:108px!important;
    }

    .nei-kind{
        width:18px!important;
        min-width:18px!important;
        font-size:5px!important;
    }

    .nei-body{
        min-height:108px!important;
        padding:9px 68px 9px 9px!important;
    }

    .nei-body h2{
        font-size:12px!important;
        margin-top:6px!important;
    }

    .nei-body p{
        font-size:6.5px!important;
    }

    .nei-meta{
        font-size:5.8px!important;
    }

    .nei-actions{
        width:57px!important;
        gap:7px!important;
    }

    .nei-actions .publish-control{
        width:38px!important;
        height:23px!important;
    }

    .nei-actions .publish-control::before{
        width:15px!important;
        height:15px!important;
    }

    .nei-actions .publish-control.is-published::before{
        left:19px!important;
    }

    .nei-actions .delete-control{
        width:36px!important;
        height:36px!important;
    }

    .nei-actions .delete-control i{
        font-size:13px!important;
    }

    .nei-search{
        flex-direction:column!important;
    }

    .nei-search button{
        width:100%!important;
    }
}

/* No mobile motion */
@media(prefers-reduced-motion:reduce){
    .nei-card,
    .nei-card *,
    .nei-search *,
    .nei-new{
        animation:none!important;
        transition:none!important;
    }
}
</style>
@endpush

