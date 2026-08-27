@extends('layouts.portal')

@section('title', 'Social Media')

@section('content')
<section class="social-hero">
    <div>
        <span class="eyebrow">PUBLIC WEBSITE</span>
        <h1>Social Media</h1>
        <p>Control the social icons shown in the public website header. Add any platform, choose its Font Awesome icon, set the order, and switch visibility without touching code.</p>
    </div>
    <div class="social-count"><strong>{{ $links->where('is_active', true)->count() }}</strong><span>active links</span></div>
</section>

@if(session('status'))
    <div class="flash">{{ session('status') }}</div>
@endif

<div class="social-grid">
    <section class="panel form-panel">
        <div class="panel-head">
            <div><span class="eyebrow">ADD PLATFORM</span><h2>New social link</h2></div>
            <i class="fa-solid fa-plus"></i>
        </div>
        <form method="POST" action="{{ route('admin.social-links.store') }}" class="social-form">
            @csrf
            <label>Platform name<input name="label" value="{{ old('label') }}" placeholder="Facebook" required></label>
            <label>Profile URL<input type="url" name="url" value="{{ old('url') }}" placeholder="https://facebook.com/your-page" required></label>
            <label>Font Awesome icon<input name="icon" value="{{ old('icon', 'fa-brands fa-facebook-f') }}" placeholder="fa-brands fa-facebook-f" required></label>
            <div class="form-row">
                <label>Order<input type="number" name="sort_order" min="0" max="9999" value="{{ old('sort_order', 0) }}"></label>
                <label class="switch-row"><span>Show on website</span><input type="checkbox" name="is_active" value="1" checked><i></i></label>
            </div>
            <button class="primary-btn" type="submit"><i class="fa-solid fa-plus"></i> Add social link</button>
        </form>
        <div class="icon-help"><strong>Popular icons</strong><span>Facebook: <code>fa-brands fa-facebook-f</code></span><span>Instagram: <code>fa-brands fa-instagram</code></span><span>YouTube: <code>fa-brands fa-youtube</code></span><span>LinkedIn: <code>fa-brands fa-linkedin-in</code></span><span>TikTok: <code>fa-brands fa-tiktok</code></span></div>
    </section>

    <section class="panel list-panel">
        <div class="panel-head">
            <div><span class="eyebrow">CURRENT LINKS</span><h2>Header social icons</h2></div>
            <span class="data-badge">{{ $links->count() }} total</span>
        </div>
        @forelse($links as $link)
            <article class="social-item">
                <div class="social-preview"><i class="{{ $link->icon }}" aria-hidden="true"></i></div>
                <form method="POST" action="{{ route('admin.social-links.update', $link) }}" class="item-form">
                    @csrf @method('PATCH')
                    <div class="item-fields">
                        <label>Name<input name="label" value="{{ $link->label }}" required></label>
                        <label>URL<input type="url" name="url" value="{{ $link->url }}" required></label>
                        <label>Icon<input name="icon" value="{{ $link->icon }}" required></label>
                        <label>Order<input type="number" name="sort_order" min="0" max="9999" value="{{ $link->sort_order }}"></label>
                    </div>
                    <div class="item-actions">
                        <label class="mini-switch"><input type="checkbox" name="is_active" value="1" {{ $link->is_active ? 'checked' : '' }}><span>{{ $link->is_active ? 'Visible' : 'Hidden' }}</span></label>
                        <button class="save-btn" type="submit"><i class="fa-solid fa-check"></i> Save</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.social-links.destroy', $link) }}" onsubmit="return confirm('Remove this social media link?')">
                    @csrf @method('DELETE')
                    <button class="delete-btn" type="submit" aria-label="Delete {{ $link->label }}"><i class="fa-solid fa-trash"></i></button>
                </form>
            </article>
        @empty
            <div class="empty"><i class="fa-solid fa-share-nodes"></i><strong>No social links yet</strong><span>Add your first platform from the panel.</span></div>
        @endforelse
    </section>
</div>
@endsection

@push('styles')
<style>
.social-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;padding:6px 0 22px}.social-hero h1{font-size:clamp(30px,4vw,46px);margin:7px 0}.social-hero p{max-width:720px;color:#7f9eaa;font-size:12px;line-height:1.7}.social-count{min-width:110px;padding:14px 16px;border:1px solid var(--line);border-radius:15px;background:rgba(67,194,229,.05);text-align:center}.social-count strong{display:block;font-size:25px}.social-count span{color:#7595a2;font-size:9px}.social-grid{display:grid;grid-template-columns:360px 1fr;gap:13px}.panel{border:1px solid var(--line);border-radius:20px;padding:21px;background:linear-gradient(145deg,rgba(9,39,55,.86),rgba(5,22,32,.92));box-shadow:0 16px 38px rgba(0,0,0,.13)}.panel-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}.panel-head h2{margin:5px 0;font-size:17px}.panel-head>i{color:#5fd5ef}.data-badge{font-size:9px;color:#86a8b5;padding:6px 8px;border:1px solid var(--line);border-radius:999px}.social-form{display:grid;gap:12px;margin-top:18px}.social-form label,.item-fields label{display:grid;gap:6px;color:#87a5b0;font-size:10px}.social-form input,.item-fields input{width:100%;border:1px solid rgba(104,204,235,.14);background:#061923;color:#e5f5f8;border-radius:10px;padding:10px 11px;font-size:11px;outline:none}.social-form input:focus,.item-fields input:focus{border-color:rgba(91,215,239,.45);box-shadow:0 0 0 3px rgba(91,215,239,.07)}.form-row{display:grid;grid-template-columns:1fr 1.4fr;gap:10px}.switch-row{display:flex!important;align-items:center;justify-content:space-between;padding:0 8px}.switch-row input,.mini-switch input{display:none}.switch-row i{width:36px;height:20px;border-radius:99px;background:#172a34;position:relative;border:1px solid var(--line)}.switch-row i:after{content:"";position:absolute;width:14px;height:14px;top:2px;left:2px;border-radius:50%;background:#6f8b95;transition:.2s}.switch-row input:checked+i{background:#1b9fbd}.switch-row input:checked+i:after{left:18px;background:#eaffff}.primary-btn,.save-btn,.delete-btn{border:1px solid rgba(104,204,235,.16);border-radius:10px;cursor:pointer}.primary-btn{min-height:40px;background:linear-gradient(100deg,#1e9fc0,#2aafca);color:#fff;font-weight:750}.icon-help{display:grid;gap:5px;margin-top:18px;padding:12px;border:1px solid rgba(104,204,235,.08);border-radius:12px;color:#6f8e99;font-size:9px}.icon-help strong{color:#91b0ba;font-size:10px}.icon-help code{color:#8ad9e9}.social-item{display:grid;grid-template-columns:42px 1fr 38px;gap:12px;align-items:start;padding:14px 0;border-top:1px solid rgba(255,255,255,.055)}.social-preview{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;background:rgba(67,194,229,.08);border:1px solid rgba(104,204,235,.13);color:#76d9ef;font-size:17px}.item-form{min-width:0}.item-fields{display:grid;grid-template-columns:1fr 1.7fr 1.3fr 70px;gap:8px}.item-actions{display:flex;align-items:center;gap:8px;margin-top:9px}.mini-switch{display:flex;align-items:center;gap:7px;color:#7696a2;font-size:9px;cursor:pointer}.mini-switch span:before{content:"";display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:5px;background:#526974}.mini-switch input:checked+span:before{background:#57d8aa}.save-btn{padding:8px 11px;background:rgba(67,194,229,.07);color:#a9dbe4;font-size:10px}.delete-btn{width:38px;height:38px;background:rgba(255,80,100,.04);color:#d98a96}.empty{min-height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;color:#6e8f9b;text-align:center}.empty i{font-size:28px;color:#58cde9}.empty strong{color:#9bb7c1;font-size:13px}.empty span{font-size:10px}@media(max-width:1000px){.social-grid{grid-template-columns:1fr}.item-fields{grid-template-columns:1fr 1.7fr 1.3fr 70px}}@media(max-width:650px){.social-hero{align-items:flex-start}.social-count{min-width:85px}.social-grid{gap:10px}.panel{padding:16px}.item-fields{grid-template-columns:1fr 1fr}.item-fields label:nth-child(2){grid-column:1/-1}.social-item{grid-template-columns:38px 1fr 34px;gap:8px}.social-preview{width:38px;height:38px}.delete-btn{width:34px;height:34px}.form-row{grid-template-columns:1fr}.item-actions{justify-content:space-between}}
</style>
@endpush
