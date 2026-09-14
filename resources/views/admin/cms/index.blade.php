@extends('layouts.portal')

@section('title', 'Page Builder')

@section('content')
<div class="pb-index">
    <header class="pbi-header">
        <div>
            <div class="pbi-kicker"><i class="fa-solid fa-layer-group"></i> WEBSITE · PAGE BUILDER</div>
            <h1>Pages</h1>
            <p>Create responsive pages with structured sections while inheriting the live global header, footer and theme framework.</p>
        </div>
        <a class="pbi-primary" href="{{ route('admin.page-builder.create') }}"><i class="fa-solid fa-plus"></i> New Page</a>
    </header>

    @if(session('status'))<div class="pbi-alert success"><i class="fa-solid fa-circle-check"></i>{{ session('status') }}</div>@endif

    <section class="pbi-framework">
        <div class="pbi-framework-icon"><i class="fa-solid fa-globe"></i></div>
        <div><strong>Global framework is active</strong><span>Every Page Builder page uses the same live navigation, branding, typography, colors and footer. Change the global theme once; pages stay synchronized.</span></div>
        <div class="pbi-framework-tags"><b><i class="fa-solid fa-check"></i> Header</b><b><i class="fa-solid fa-check"></i> Footer</b><b><i class="fa-solid fa-check"></i> Theme</b></div>
    </section>

    @if($pages->count())
        <section class="pbi-list" aria-label="Pages list">
            @foreach($pages as $page)
                @php
                    $isCms = $page instanceof \App\Models\CmsPage;
                    $isPublished = ($page->is_published ?? false) || ($page->status ?? '') === 'published';
                @endphp
                <article class="pbi-card" tabindex="0" role="link" data-edit-url="{{ $page->edit_url }}" aria-label="Edit {{ $page->title }}">
                    <div class="pbi-icon" aria-hidden="true"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="pbi-kind"><span>{{ $isCms ? 'GLOBAL CMS' : 'CONTENT' }}</span></div>
                    <div class="pbi-body">
                        <div class="pbi-heading">
                            <h2>{{ $page->title }}</h2>
                            <span class="pbi-status {{ $isPublished ? 'published' : 'draft' }}"><i></i>{{ $isPublished ? 'Published' : 'Draft' }}</span>
                        </div>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($page->excerpt ?? $page->content ?? ''), 150) ?: 'No description added yet.' }}</p>
                        <div class="pbi-meta">
                            <span><i class="fa-solid fa-database"></i>{{ $page->content_source }}</span>
                            <span><i class="fa-solid fa-link"></i>/pages/{{ $page->slug }}</span>
                            <span><i class="fa-regular fa-clock"></i>{{ optional($page->updated_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="pbi-actions" aria-label="Page actions">
                        <form method="POST" action="{{ $page->toggle_url }}" class="pbi-toggle-form">
                            @csrf @method('PATCH')
                            <button type="submit" class="pbi-toggle {{ $isPublished ? 'is-published' : 'is-draft' }}" aria-label="{{ $isPublished ? 'Unpublish' : 'Publish' }} {{ $page->title }}" title="{{ $isPublished ? 'Unpublish' : 'Publish' }}">
                                <span aria-hidden="true"></span>
                            </button>
                        </form>
                        <form method="POST" action="{{ $page->delete_url }}" class="pbi-delete-form" onsubmit="return confirm('Delete this page? This cannot be undone.');">
                            @csrf @method('DELETE')
                            <button class="pbi-delete" type="submit" aria-label="Delete {{ $page->title }}" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                        </form>
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
.pb-index{max-width:1400px;margin:0 auto;padding:4px 0 48px;color:#ecf9fb;min-width:0}.pbi-header{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;padding:8px 0 20px}.pbi-kicker{font-size:9px;letter-spacing:.15em;font-weight:900;color:#49d5ef}.pbi-header h1{margin:5px 0 6px;font-size:36px;letter-spacing:-.8px}.pbi-header p{margin:0;color:#7897a3;font-size:11px;max-width:700px;line-height:1.65}.pbi-primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:42px;padding:0 15px;border-radius:11px;background:linear-gradient(135deg,#20b978 0%,#27c79d 45%,#1b9eb8 100%);border:1px solid rgba(103,231,161,.28);color:#f5fffb;text-decoration:none;font-size:11px;font-weight:850;box-shadow:0 12px 32px rgba(20,145,105,.18)}.pbi-primary:hover{filter:brightness(1.06)}.pbi-alert{display:flex;align-items:center;gap:9px;padding:11px 13px;border-radius:10px;margin-bottom:14px;font-size:10px}.pbi-alert.success{color:#9ce5c9;background:rgba(66,201,155,.07);border:1px solid rgba(66,201,155,.16)}.pbi-framework{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:12px;padding:14px 16px;border-radius:14px;border:1px solid rgba(73,213,239,.15);background:linear-gradient(135deg,rgba(73,213,239,.07),rgba(6,25,34,.96));margin-bottom:16px;min-width:0}.pbi-framework-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:11px;background:rgba(73,213,239,.1);color:#62dcef}.pbi-framework strong,.pbi-framework span{display:block}.pbi-framework strong{font-size:11px}.pbi-framework span{margin-top:3px;color:#708e99;font-size:9px;line-height:1.45}.pbi-framework-tags{display:flex;gap:5px;flex-wrap:wrap}.pbi-framework-tags b{padding:6px 8px;border-radius:8px;background:rgba(66,201,155,.06);border:1px solid rgba(66,201,155,.13);color:#79dcb8;font-size:8px}.pbi-list{display:grid;gap:9px;min-width:0}.pbi-card{position:relative;display:grid;grid-template-columns:70px 92px minmax(0,1fr) 112px;align-items:center;min-height:116px;overflow:hidden;border:1px solid var(--ff-card-border,rgba(103,208,234,.12));border-radius:18px;background:var(--ff-card-bg-soft,linear-gradient(145deg,#091e29,#06151e));box-shadow:var(--ff-card-shadow-raised,0 22px 70px rgba(0,0,0,.28));min-width:0;cursor:pointer;transition:transform .18s,border-color .18s,box-shadow .18s}.pbi-card:hover{transform:translateY(-1px);border-color:var(--ff-card-border-hover,rgba(100,224,192,.28));box-shadow:0 16px 44px rgba(0,0,0,.24)}.pbi-card:focus-visible{outline:2px solid rgba(85,217,239,.65);outline-offset:3px}.pbi-icon{width:48px;height:48px;display:grid;place-items:center;margin-left:16px;border-radius:13px;background:rgba(50,229,138,.09);border:1px solid rgba(50,229,138,.12);color:#79ffb5;font-size:20px}.pbi-kind{height:76px;display:flex;align-items:center;justify-content:center;border-left:1px solid rgba(72,216,241,.1);border-right:1px solid rgba(72,216,241,.1);color:#51d8f0;font-size:7px;font-weight:900;letter-spacing:.13em;writing-mode:vertical-rl;transform:rotate(180deg);text-align:center}.pbi-body{min-width:0;padding:15px 20px}.pbi-heading{display:flex;align-items:center;gap:10px;min-width:0}.pbi-heading h2{margin:0;color:#edfaff;font-size:17px;line-height:1.3;letter-spacing:-.02em;overflow-wrap:anywhere}.pbi-status{display:inline-flex;align-items:center;gap:6px;flex:0 0 auto;padding:6px 9px;border-radius:999px;font-size:7px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}.pbi-status i{width:6px;height:6px;border-radius:50%;background:currentColor;box-shadow:0 0 8px currentColor}.pbi-status.published{color:#27e394;background:rgba(39,227,148,.12)}.pbi-status.draft{color:#c9d5d8;background:rgba(104,130,139,.16)}.pbi-body p{margin:5px 0 8px;color:#7897a3;font-size:9px;line-height:1.5;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.pbi-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap;color:#587682;font-size:8px;min-width:0}.pbi-meta span{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.pbi-meta i{margin-right:5px;color:#52d7ee}.pbi-actions{height:100%;display:flex;align-items:center;justify-content:center;gap:12px;border-left:1px solid rgba(72,216,241,.1);background:rgba(2,16,23,.22);padding:0 13px}.pbi-actions form{margin:0}.pbi-toggle,.pbi-delete{box-sizing:border-box;display:flex;align-items:center;justify-content:center;position:relative;cursor:pointer}.pbi-toggle{width:54px;height:30px;min-width:54px;padding:0;border-radius:999px;border:1px solid rgba(103,208,234,.18);background:#294653;color:transparent;box-shadow:inset 0 1px 3px rgba(0,0,0,.32);overflow:hidden;appearance:none;-webkit-appearance:none;transition:background .18s,border-color .18s}.pbi-toggle>span{position:absolute;top:4px;left:4px;width:20px;height:20px;border-radius:50%;background:#c6d1d5;box-shadow:0 1px 4px rgba(0,0,0,.38);transition:left .18s ease,background .18s ease,box-shadow .18s ease}.pbi-toggle.is-published{background:#159463;border-color:rgba(82,236,170,.48);box-shadow:inset 0 1px 3px rgba(0,0,0,.18),0 0 14px rgba(39,227,148,.10)}.pbi-toggle.is-published>span{left:29px;background:#f3fff9;box-shadow:0 1px 4px rgba(0,0,0,.28),0 0 9px rgba(95,255,189,.32)}.pbi-toggle:hover{transform:none}.pbi-toggle:focus-visible,.pbi-delete:focus-visible{outline:2px solid rgba(85,217,239,.78);outline-offset:3px}.pbi-delete{width:54px;height:54px;border-radius:14px;border:1px solid rgba(255,105,124,.17);background:rgba(6,28,38,.72);color:#ff8292;font-size:18px}.pbi-delete:hover{border-color:rgba(255,105,124,.35);transform:translateY(-1px)}.pbi-pagination{margin-top:15px;min-width:0;overflow-x:auto;overflow-y:hidden}.pbi-pagination nav{min-width:max-content}.pbi-empty{display:grid;justify-items:center;text-align:center;padding:70px 20px;border:1px dashed rgba(103,208,234,.18);border-radius:16px;background:linear-gradient(145deg,rgba(9,31,43,.6),rgba(5,19,28,.7))}.pbi-empty-icon{width:62px;height:62px;display:grid;place-items:center;border-radius:17px;background:rgba(73,213,239,.07);border:1px solid rgba(73,213,239,.15);color:#49d5ef;font-size:22px}.pbi-empty h2{margin:15px 0 6px;font-size:19px}.pbi-empty p{max-width:570px;margin:0 0 18px;color:#718e99;font-size:10px;line-height:1.65}
@media(max-width:1050px){.pbi-card{grid-template-columns:62px 78px minmax(0,1fr) 100px}.pbi-icon{margin-left:13px}.pbi-body{padding:14px}.pbi-actions{gap:9px;padding:0 10px}.pbi-toggle{width:50px;min-width:50px}.pbi-toggle.is-published>span{left:26px}.pbi-delete{width:48px;height:48px}}
@media(max-width:720px){.pbi-header{align-items:flex-start;flex-direction:column}.pbi-primary{width:100%}.pbi-framework{grid-template-columns:auto minmax(0,1fr)}.pbi-framework-tags{grid-column:1/-1}.pbi-card{grid-template-columns:58px 70px minmax(0,1fr) 82px;min-height:106px;border-radius:16px}.pbi-icon{width:42px;height:42px;margin-left:10px;font-size:17px}.pbi-kind{height:68px;font-size:6px}.pbi-body{padding:11px}.pbi-heading{align-items:flex-start;flex-direction:column;gap:5px}.pbi-heading h2{font-size:14px}.pbi-status{padding:5px 7px;font-size:6px}.pbi-body p{font-size:8px;margin:4px 0 6px}.pbi-meta{gap:7px;font-size:7px}.pbi-meta span:nth-child(2){display:none}.pbi-actions{gap:7px;padding:0 7px}.pbi-toggle{width:46px;min-width:46px;height:28px}.pbi-toggle>span{width:18px;height:18px;top:4px;left:4px}.pbi-toggle.is-published>span{left:24px}.pbi-delete{width:44px;height:44px;border-radius:12px;font-size:16px}}
@media(max-width:480px){.pb-index{padding-bottom:30px}.pbi-card{grid-template-columns:48px minmax(0,1fr) 72px;align-items:stretch}.pbi-kind{display:none}.pbi-icon{align-self:center;margin-left:9px;width:38px;height:38px;border-radius:11px;font-size:16px}.pbi-body{padding:11px 8px}.pbi-heading{gap:4px}.pbi-heading h2{font-size:13px}.pbi-body p{white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}.pbi-meta span:first-child{display:none}.pbi-meta{font-size:6.5px}.pbi-actions{flex-direction:column;justify-content:center;gap:8px;padding:0 7px}.pbi-toggle{width:46px!important;min-width:46px!important}.pbi-delete{width:42px;height:42px}.pbi-framework{padding:12px}.pbi-framework span{font-size:8px}.pbi-framework-tags b{font-size:7px}}
@media(max-width:350px){.pbi-card{grid-template-columns:42px minmax(0,1fr) 66px}.pbi-icon{margin-left:7px;width:34px;height:34px}.pbi-body{padding-left:7px;padding-right:5px}.pbi-actions{padding:0 5px}.pbi-toggle{width:42px!important;min-width:42px!important;height:26px}.pbi-toggle>span{width:16px;height:16px;top:4px;left:4px}.pbi-toggle.is-published>span{left:22px}.pbi-delete{width:38px;height:38px}}
@media(prefers-reduced-motion:reduce){.pbi-card,.pbi-toggle,.pbi-delete{transition:none}}
</style>
@endpush

@push('scripts')
<script>
(function(){
    function initPageBuilderCards(){
        document.querySelectorAll('.pbi-card[data-edit-url]').forEach(function(card){
            if(card.dataset.editBound==='1') return;
            card.dataset.editBound='1';
            function openEdit(){ window.location.href=card.dataset.editUrl; }
            card.addEventListener('click',function(event){
                if(event.target.closest('a,button,form,input,select,textarea,label')) return;
                openEdit();
            });
            card.addEventListener('keydown',function(event){
                if(event.key==='Enter' || event.key===' '){
                    if(event.target!==card) return;
                    event.preventDefault();
                    openEdit();
                }
            });
        });
    }
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',initPageBuilderCards,{once:true}); else initPageBuilderCards();
})();
</script>
@endpush
