@extends('layouts.portal')

@section('title', 'Social Media')

@section('content')
<section class="social-hero">
    <div>
        <span class="eyebrow">PUBLIC WEBSITE · SOCIAL</span>
        <h1>Social Media</h1>
        <p>Choose a platform, paste its profile or channel link, and arrange the icons exactly how they should appear on the website. No Font Awesome codes required.</p>
    </div>
    <div class="social-count"><strong>{{ $links->where('is_active', true)->count() }}</strong><span>active links</span></div>
</section>

@if(session('status'))<div class="flash">{{ session('status') }}</div>@endif
@if($errors->any())<div class="flash error">{{ $errors->first() }}</div>@endif

<div class="social-grid">
    <section class="panel form-panel">
        <div class="panel-head">
            <div><span class="eyebrow">ADD PLATFORM</span><h2>New social link</h2></div>
            <i class="fa-solid fa-share-nodes"></i>
        </div>

        <form method="POST" action="{{ route('admin.social-links.store') }}" class="social-form">
            @csrf
            <label>Social platform
                <select name="platform" id="social-platform" required>
                    <option value="" disabled {{ old('platform') ? '' : 'selected' }}>Select a platform</option>
                    @foreach($platforms as $key => $platform)
                        <option value="{{ $key }}" data-icon="{{ $platform['icon'] }}" data-color="{{ $platform['color'] }}" {{ old('platform') === $key ? 'selected' : '' }}>{{ $platform['label'] }}</option>
                    @endforeach
                </select>
            </label>

            <div class="platform-preview" id="platform-preview" hidden>
                <span class="preview-icon"><i></i></span>
                <span><strong></strong><small>Official platform icon</small></span>
            </div>

            <label>Profile / channel URL
                <input type="url" name="url" value="{{ old('url') }}" placeholder="https://facebook.com/your-page" required>
            </label>

            <label class="switch-row"><span>Show on website</span><input type="checkbox" name="is_active" value="1" checked><i></i></label>

            <button class="primary-btn" type="submit"><i class="fa-solid fa-plus"></i> Add social link</button>
        </form>

        <div class="platform-note"><i class="fa-solid fa-circle-info"></i><span>The platform selection automatically supplies the correct brand icon and color. You only need to paste your link.</span></div>
    </section>

    <section class="panel list-panel">
        <div class="panel-head">
            <div><span class="eyebrow">DISPLAY ORDER</span><h2>Website social icons</h2></div>
            <span class="data-badge">{{ $links->count() }} total</span>
        </div>
        <div class="drag-hint"><i class="fa-solid fa-grip-vertical"></i> Drag any item to change its position</div>

        <div id="social-list" class="social-list">
        @forelse($links as $link)
            @php($meta = $link->platformMeta())
            <article class="social-item" draggable="true" data-id="{{ $link->id }}">
                <div class="drag-handle" title="Drag to reorder" aria-label="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></div>
                <div class="social-preview" style="--brand-color:{{ $meta['color'] }}"><i class="{{ $meta['icon'] }}" aria-hidden="true"></i></div>

                <form method="POST" action="{{ route('admin.social-links.update', $link) }}" class="item-form">
                    @csrf @method('PATCH')
                    <div class="item-fields">
                        <label>Platform
                            <select name="platform" required>
                                @foreach($platforms as $key => $platform)
                                    <option value="{{ $key }}" {{ $link->platform === $key ? 'selected' : '' }}>{{ $platform['label'] }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>URL
                            <input type="url" name="url" value="{{ $link->url }}" required>
                        </label>

                    </div>
                    <div class="item-actions">
                        <label class="mini-switch"><input type="checkbox" name="is_active" value="1" {{ $link->is_active ? 'checked' : '' }}><span>{{ $link->is_active ? 'Visible' : 'Hidden' }}</span></label>
                        <button class="save-btn" type="submit"><i class="fa-solid fa-check"></i> Save</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.social-links.destroy', $link) }}" onsubmit="return confirm('Remove this social media link?')">
                    @csrf @method('DELETE')
                    <button class="delete-btn" type="submit" aria-label="Delete {{ $meta['label'] }}"><i class="fa-solid fa-trash"></i></button>
                </form>
            </article>
        @empty
            <div class="empty"><i class="fa-solid fa-share-nodes"></i><strong>No social links yet</strong><span>Add your first platform from the panel.</span></div>
        @endforelse
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.social-hero{
    position:relative;
    display:grid;
    grid-template-columns:minmax(0,1fr) auto;
    align-items:start;
    gap:24px;
    padding:0 0 18px;
    margin:0 0 4px;
}
.social-hero>div:first-child{min-width:0}
.social-hero h1{
    font-size:clamp(30px,4vw,46px);
    line-height:1.05;
    margin:6px 0 9px;
    letter-spacing:-.025em;
}
.social-hero p{
    max-width:760px;
    margin:0;
    color:#7f9eaa;
    font-size:12px;
    line-height:1.65;
}
.social-count{
    min-width:104px;
    padding:12px 15px;
    margin-top:2px;
    border:1px solid var(--line);
    border-radius:15px;
    background:linear-gradient(145deg,rgba(67,194,229,.08),rgba(67,194,229,.025));
    text-align:center;
    box-shadow:0 10px 28px rgba(0,0,0,.10);
}
.social-count strong{display:block;font-size:24px;line-height:1.1}
.social-count span{display:block;margin-top:4px;color:#7595a2;font-size:9px}
.flash{padding:11px 13px;margin-bottom:12px;border-radius:11px;background:rgba(67,194,137,.1);color:#a8e5ca;font-size:10px}
.flash.error{background:rgba(255,90,110,.08);color:#ffb1ba}
.social-grid{display:grid;grid-template-columns:minmax(320px,360px) minmax(0,1fr);gap:13px;align-items:start}
.panel{
    border:1px solid var(--line);
    border-radius:20px;
    padding:21px;
    background:linear-gradient(145deg,rgba(9,39,55,.86),rgba(5,22,32,.92));
    box-shadow:0 16px 38px rgba(0,0,0,.13);
}
.panel-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
.panel-head h2{margin:5px 0;font-size:17px}
.panel-head>i{color:#5fd5ef}
.data-badge{font-size:9px;color:#86a8b5;padding:6px 8px;border:1px solid var(--line);border-radius:999px}
.social-form{display:grid;gap:12px;margin-top:18px}
.social-form label,.item-fields label{display:grid;gap:6px;color:#87a5b0;font-size:10px}
.social-form input,.social-form select,.item-fields input,.item-fields select{
    width:100%;
    border:1px solid rgba(104,204,235,.14);
    background:#061923;
    color:#e5f5f8;
    border-radius:10px;
    padding:10px 11px;
    font-size:11px;
    outline:none;
}
.social-form select,.item-fields select{appearance:auto}
.social-form input:focus,.social-form select:focus,.item-fields input:focus,.item-fields select:focus{border-color:rgba(91,215,239,.45);box-shadow:0 0 0 3px rgba(91,215,239,.07)}
.switch-row{display:flex!important;align-items:center;justify-content:space-between;padding:0 8px}
.switch-row input,.mini-switch input{display:none}
.switch-row i{width:36px;height:20px;border-radius:99px;background:#172a34;position:relative;border:1px solid var(--line)}
.switch-row i:after{content:"";position:absolute;width:14px;height:14px;top:2px;left:2px;border-radius:50%;background:#6f8b95;transition:.2s}
.switch-row input:checked+i{background:#1b9fbd}
.switch-row input:checked+i:after{left:18px;background:#eaffff}
.primary-btn,.save-btn,.delete-btn{border:1px solid rgba(104,204,235,.16);border-radius:10px;cursor:pointer}
.primary-btn{min-height:40px;background:linear-gradient(100deg,#1e9fc0,#2aafca);color:#fff;font-weight:750}
.platform-preview{display:flex;align-items:center;gap:11px;padding:10px 12px;border:1px solid rgba(104,204,235,.10);border-radius:12px;background:rgba(67,194,229,.035)}
.platform-preview .preview-icon{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;background:rgba(67,194,229,.08);color:var(--brand-color,#51d8f0);font-size:16px}
.platform-preview strong,.platform-preview small{display:block}
.platform-preview strong{font-size:11px;color:#dff4f7}
.platform-preview small{font-size:8px;color:#668692;margin-top:2px}
.platform-note{display:flex;gap:8px;margin-top:15px;padding:10px 11px;border-radius:11px;background:rgba(67,194,229,.035);border:1px solid rgba(104,204,235,.08);color:#708e99;font-size:9px;line-height:1.55}
.platform-note i{color:#59cee8;margin-top:1px}
.drag-hint{display:flex;align-items:center;gap:7px;margin:13px 0 2px;color:#678792;font-size:9px}
.drag-hint i{color:#5bb8ca}
.social-list{display:grid;gap:8px}
.social-item{display:grid;grid-template-columns:24px 46px minmax(0,1fr) 38px;gap:11px;align-items:start;padding:13px 0;border-top:1px solid rgba(255,255,255,.055);transition:transform .18s,border-color .18s,opacity .18s}
.social-item:hover{background:rgba(67,194,229,.018)}
.social-item.dragging{opacity:.45;transform:scale(.99)}
.drag-handle{display:grid;place-items:center;height:42px;color:#537883;cursor:grab}
.drag-handle:active{cursor:grabbing}
.social-preview{width:46px;height:46px;display:grid;place-items:center;border-radius:13px;background:rgba(67,194,229,.07);border:1px solid rgba(104,204,235,.13);color:#76d9ef;font-size:18px;transition:color .2s,background .2s,border-color .2s}
.social-item:hover .social-preview{color:var(--brand-color);background:color-mix(in srgb,var(--brand-color) 10%,transparent);border-color:color-mix(in srgb,var(--brand-color) 28%,transparent)}
.item-form{min-width:0}
.item-fields{display:grid;grid-template-columns:1fr 2fr;gap:8px}
.item-actions{display:flex;align-items:center;gap:8px;margin-top:9px}
.mini-switch{display:flex;align-items:center;gap:7px;color:#7696a2;font-size:9px;cursor:pointer}
.mini-switch span:before{content:"";display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:5px;background:#526974}
.mini-switch input:checked+span:before{background:#57d8aa}
.save-btn{padding:8px 11px;background:rgba(67,194,229,.07);color:#a9dbe4;font-size:10px}
.delete-btn{width:38px;height:38px;background:rgba(255,80,100,.04);color:#d98a96}
.empty{min-height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;color:#6e8f9b;text-align:center}
.empty i{font-size:28px;color:#58cde9}
.empty strong{color:#9bb7c1;font-size:13px}
.empty span{font-size:10px}

@media(max-width:1000px){
    .social-grid{grid-template-columns:1fr}
}
@media(max-width:650px){
    .social-hero{
        display:block;
        padding:0 0 14px;
        margin-bottom:0;
    }
    .social-hero h1{
        padding-right:105px;
        font-size:clamp(29px,9vw,38px);
        margin-top:5px;
    }
    .social-hero p{font-size:11px;line-height:1.6}
    .social-count{
        position:absolute;
        top:0;
        right:0;
        min-width:86px;
        padding:9px 10px;
        border-radius:13px;
    }
    .social-count strong{font-size:20px}
    .social-count span{font-size:8px}
    .social-grid{gap:10px}
    .panel{padding:16px;border-radius:18px}
    .item-fields{grid-template-columns:1fr 1fr}
    .item-fields label:nth-child(2){grid-column:1/-1}
    .social-item{grid-template-columns:22px 42px minmax(0,1fr) 34px;gap:8px}
    .social-preview{width:42px;height:42px}
    .delete-btn{width:34px;height:34px}
    .form-row{grid-template-columns:1fr}
    .item-actions{justify-content:space-between}
}
</style>
@endpush

@push('scripts')
<script>
(function(){
    const list=document.getElementById('social-list');
    const select=document.getElementById('social-platform');
    const preview=document.getElementById('platform-preview');

    if(select&&preview){
        const icon=preview.querySelector('i'), name=preview.querySelector('strong');
        const sync=()=>{
            const option=select.options[select.selectedIndex];
            if(!option||!option.value){preview.hidden=true;return}
            icon.className=option.dataset.icon||'fa-solid fa-globe';
            name.textContent=option.textContent.trim();
            preview.style.setProperty('--brand-color',option.dataset.color||'#51d8f0');
            preview.hidden=false;
        };
        select.addEventListener('change',sync);
        sync();
    }

    document.querySelectorAll('.item-fields select').forEach(select=>{
        select.addEventListener('change',function(){
            const item=this.closest('.social-item'), option=this.options[this.selectedIndex], preview=item&&item.querySelector('.social-preview');
            if(!preview||!option)return;
            preview.style.setProperty('--brand-color',option.dataset?.color||'#51d8f0');
        });
    });

    if(!list)return;
    let dragged=null;
    list.querySelectorAll('.social-item').forEach(card=>{
        card.addEventListener('dragstart',()=>{dragged=card;card.classList.add('dragging')});
        card.addEventListener('dragend',async()=>{
            card.classList.remove('dragging');
            const order=[...list.querySelectorAll('.social-item')].map(x=>x.dataset.id);
            try{
                const response=await fetch('{{ route('admin.social-links.reorder') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:JSON.stringify({order})});
                if(!response.ok)throw new Error();
            }catch(e){window.location.reload()}
            dragged=null;
        });
        card.addEventListener('dragover',e=>{
            e.preventDefault();
            if(!dragged||dragged===card)return;
            const rect=card.getBoundingClientRect();
            if(e.clientY<rect.top+rect.height/2)card.before(dragged);else card.after(dragged);
        });
    });
})();
</script>
@endpush