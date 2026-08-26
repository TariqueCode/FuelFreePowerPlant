@extends('layouts.portal')
@section('title','Highlights')
@section('content')
<section class="hero highlight-hero">
    <div>
        <span class="eyebrow">PUBLIC WEBSITE CONTROL</span>
        <h1>Highlights</h1>
        <p>Manage the homepage banners visitors see first. Schedule them, set an auto-close timer, or let visitors close them manually.</p>
    </div>
    <a class="action highlight-create" href="{{ route('admin.site-popups.create') }}"><i class="fa-solid fa-plus"></i> New highlight</a>
</section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif

<div class="highlight-list">
    @forelse($popups as $popup)
        @php
            $now = now();
            $state = !$popup->is_published ? 'draft' : (($popup->starts_at && $popup->starts_at->gt($now)) ? 'scheduled' : (($popup->ends_at && $popup->ends_at->lt($now)) ? 'expired' : 'live'));
            $stateLabel = ['draft'=>'Draft','scheduled'=>'Scheduled','expired'=>'Expired','live'=>'Live'][$state];
        @endphp
        <article class="highlight-card">
            <a class="highlight-media" href="{{ route('admin.site-popups.edit',$popup) }}" aria-label="Open {{ $popup->title ?: 'highlight' }}">
                <img src="{{ asset('storage/'.$popup->image_path) }}" alt="{{ $popup->title ?: 'Highlight banner' }}" loading="lazy">
            </a>
            <a class="highlight-content" href="{{ route('admin.site-popups.edit',$popup) }}">
                <div class="highlight-top">
                    <span class="status {{ $state }}"><i></i>{{ $stateLabel }}</span>
                    @if($popup->link_url)<span class="link-type"><i class="fa-solid fa-link"></i> Linked</span>@endif
                </div>
                <h2>{{ $popup->title ?: 'Untitled highlight' }}</h2>
                <div class="meta-row">
                    <span><i class="fa-regular fa-clock"></i>{{ $popup->display_seconds ? $popup->display_seconds.' sec auto-close' : 'Visitor closes' }}</span>
                    <span><i class="fa-regular fa-calendar"></i>{{ $popup->starts_at?->format('d M Y, H:i') ?: 'Immediately' }}{{ $popup->ends_at ? ' → '.$popup->ends_at->format('d M Y, H:i') : ' · No expiry' }}</span>
                </div>
            </a>
            <div class="highlight-actions">
                <form method="POST" action="{{ route('admin.site-popups.update',$popup) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="toggle" value="1">
                    <button class="toggle-btn {{ $popup->is_published ? 'active' : 'inactive' }}" type="submit" aria-label="{{ $popup->is_published ? 'Deactivate' : 'Activate' }} highlight" title="{{ $popup->is_published ? 'Deactivate' : 'Activate' }}">
                        <i class="fa-solid fa-power-off"></i><span>{{ $popup->is_published ? 'Active' : 'Activate' }}</span>
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.site-popups.destroy',$popup) }}" onsubmit="return confirm('Delete this highlight?')">
                    @csrf @method('DELETE')
                    <button class="delete-btn" type="submit" aria-label="Delete highlight" title="Delete highlight"><i class="fa-solid fa-trash-can"></i></button>
                </form>
            </div>
        </article>
    @empty
        <div class="empty-state">
            <div class="empty-icon"><i class="fa-regular fa-image"></i></div>
            <div class="empty-copy">
                <strong>No highlights yet</strong>
                <span>Create a homepage banner to make important announcements visible first.</span>
            </div>
        </div>
    @endforelse
</div>

<div class="pagination">{{ $popups->links() }}</div>
@endsection

@push('styles')
<style>
.highlight-hero{margin-bottom:18px}.highlight-create{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;border-radius:12px;text-decoration:none!important;font-weight:800}.highlight-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.highlight-card{position:relative;display:grid;grid-template-columns:150px minmax(0,1fr) 92px;min-height:150px;overflow:hidden;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,37,50,.9),rgba(3,19,27,.94));transition:transform .2s,border-color .2s}.highlight-card:hover{transform:translateY(-2px);border-color:rgba(72,216,241,.38)}.highlight-media{display:block;width:150px;height:150px;overflow:hidden;background:#061923}.highlight-media img{width:100%;height:100%;display:block;object-fit:cover;aspect-ratio:1/1}.highlight-content{padding:17px 14px;min-width:0;color:inherit;text-decoration:none}.highlight-top{display:flex;align-items:center;gap:8px;min-height:20px}.status,.link-type{display:inline-flex;align-items:center;gap:6px;font-size:8px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.status{padding:5px 8px;border-radius:999px}.status i{width:5px;height:5px;border-radius:50%;background:currentColor}.status.live{color:#8de8cc;background:rgba(67,194,137,.1)}.status.scheduled{color:#8bd9ff;background:rgba(67,176,229,.1)}.status.draft{color:#ffc77d;background:rgba(255,183,77,.1)}.status.expired{color:#ff9eaa;background:rgba(255,93,113,.1)}.link-type{color:#6f9aa5}.highlight-content h2{font-size:17px;line-height:1.3;margin:10px 0 9px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.meta-row{display:flex;flex-direction:column;gap:6px;color:#7898a2;font-size:9px;line-height:1.45}.meta-row span{display:flex;align-items:flex-start;gap:6px}.meta-row i{color:#58cfe7;width:11px;margin-top:1px}.highlight-actions{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border-left:1px solid rgba(116,221,239,.1);padding:8px}.highlight-actions form{margin:0;width:100%;display:flex;justify-content:center}.toggle-btn,.delete-btn{border:0;cursor:pointer}.toggle-btn{width:76px;min-height:34px;display:flex;align-items:center;justify-content:center;gap:6px;border-radius:9px;font-size:9px;font-weight:800;letter-spacing:.02em;background:rgba(72,216,241,.08);color:#74d8ee;border:1px solid rgba(72,216,241,.18)}.toggle-btn.active{color:#8de8cc;background:rgba(67,194,137,.1);border-color:rgba(67,194,137,.22)}.toggle-btn.inactive{color:#74d8ee}.toggle-btn:hover{transform:translateY(-1px);background:rgba(72,216,241,.14)}.delete-btn{width:30px;height:30px;display:grid;place-items:center;border-radius:8px;background:transparent;color:#ff9eaa;font-size:12px}.delete-btn:hover{background:rgba(255,93,113,.1)}.empty-state{grid-column:1/-1;display:flex;align-items:center;gap:14px;padding:18px 20px;border:1px solid var(--line);border-radius:16px;background:rgba(8,37,50,.38);color:#7898a2}.empty-icon{width:42px;height:42px;flex:0 0 42px;display:grid;place-items:center;border-radius:12px;background:rgba(72,216,241,.08);color:#58cfe7;font-size:18px}.empty-copy{display:flex;flex-direction:column;gap:3px}.empty-copy strong{color:var(--text);font-size:14px}.empty-copy span{font-size:9px;line-height:1.45}.pagination{margin-top:14px}@media(max-width:900px){.highlight-list{grid-template-columns:1fr}}@media(max-width:600px){.highlight-hero{margin-bottom:12px}.highlight-create{padding:10px 13px;font-size:12px}.highlight-list{gap:10px}.highlight-card{grid-template-columns:92px minmax(0,1fr) 74px;min-height:112px;border-radius:14px}.highlight-media{width:92px;height:92px;min-height:92px;align-self:start}.highlight-content{padding:11px 10px}.highlight-content h2{font-size:14px;margin:7px 0 6px;-webkit-line-clamp:2}.status,.link-type{font-size:7px}.status{padding:4px 6px}.meta-row{font-size:8px;gap:4px}.highlight-actions{padding:6px;gap:5px}.toggle-btn{width:64px;min-height:30px;font-size:8px}.toggle-btn i{font-size:9px}.delete-btn{width:28px;height:28px;font-size:11px}.empty-state{padding:13px 14px;border-radius:14px;gap:11px}.empty-icon{width:36px;height:36px;flex-basis:36px;border-radius:10px;font-size:15px}.empty-copy strong{font-size:12px}.empty-copy span{font-size:8px}}@media(max-width:380px){.highlight-card{grid-template-columns:82px minmax(0,1fr) 68px}.highlight-media{width:82px;height:82px}.highlight-content{padding:10px 8px}.highlight-content h2{font-size:13px}.meta-row{font-size:7px}.toggle-btn{width:58px;font-size:7px}}
</style>
@endpush