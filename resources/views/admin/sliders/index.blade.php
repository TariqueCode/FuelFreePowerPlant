@extends('layouts.portal')
@section('title','Slider')
@section('content')
<section class="hero slider-hero"><div><span class="eyebrow">PUBLIC WEBSITE CONTROL</span><h1>Homepage Slider</h1><p>Drag and drop the cards to set the display order. Published images rotate automatically with a smooth animation.</p></div><div class="slider-header-actions"><a class="primary" href="{{ route('admin.sliders.create') }}"><i class="fa-solid fa-plus"></i> Add slider</a><div class="profile-count"><strong>{{ $publishedCount }}</strong><span>published</span></div></div></section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
<div id="slider-save-status" class="save-status" aria-live="polite"></div>
<div class="slider-list" id="slider-list">
@forelse($sliders as $slider)
    @php $now=now(); $state=!$slider->is_published?'draft':(($slider->starts_at&&$slider->starts_at->gt($now))?'scheduled':(($slider->ends_at&&$slider->ends_at->lt($now))?'expired':'live')); @endphp
    <article class="slider-card" draggable="true" data-id="{{ $slider->id }}">
        <div class="drag-handle" title="Drag to reorder" aria-label="Drag to reorder"><i class="fa-solid fa-grip-vertical"></i></div>
        <a class="slider-media" href="{{ route('admin.sliders.edit',$slider) }}"><img src="{{ asset('storage/'.$slider->image_path) }}" alt="{{ $slider->title ?: 'Homepage slider' }}" loading="lazy"></a>
        <a class="slider-content" href="{{ route('admin.sliders.edit',$slider) }}">
            <div class="slider-top"><span class="status {{ $state }}"><i></i>{{ ucfirst($state) }}</span></div>
            <h2>{{ $slider->title ?: 'Untitled slider' }}</h2>
            <div class="meta-row"><span><i class="fa-solid fa-link"></i>{{ $slider->link_url ? 'Linked image' : 'No link' }}</span><span><i class="fa-regular fa-calendar"></i>{{ $slider->starts_at?->format('d M Y, H:i') ?: 'Immediately' }}{{ $slider->ends_at ? ' → '.$slider->ends_at->format('d M Y, H:i') : ' · No expiry' }}</span></div>
        </a>
        <div class="slider-actions">
            <form method="POST" action="{{ route('admin.sliders.update',$slider) }}">@csrf @method('PATCH')<input type="hidden" name="toggle" value="1"><button class="toggle-btn {{ $slider->is_published?'active':'inactive' }}" type="submit" title="{{ $slider->is_published?'Deactivate':'Activate' }}"><span class="toggle-track"><span class="toggle-knob"></span></span></button></form>
            <form method="POST" action="{{ route('admin.sliders.destroy',$slider) }}" onsubmit="return confirm('Delete this slider image?')">@csrf @method('DELETE')<button class="delete-btn" type="submit" title="Delete slider"><i class="fa-solid fa-trash-can"></i></button></form>
        </div>
    </article>
@empty
    <div class="empty-state"><div class="empty-icon"><i class="fa-regular fa-images"></i></div><div class="empty-copy"><strong>No slider images yet</strong><span>Add your first company image. Published images will automatically rotate above the homepage welcome message.</span></div></div>
@endforelse
</div>
<div class="pagination">{{ $sliders->links() }}</div>
@endsection
@push('styles')
<style>
.slider-hero{margin-bottom:18px}.slider-header-actions{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:stretch}.slider-header-actions .primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:11px;padding:11px 15px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;text-decoration:none;font-size:10px;font-weight:800;white-space:nowrap}.slider-header-actions .profile-count{display:flex;align-items:center;justify-content:center;gap:5px;min-width:100px;padding:0 15px;border:1px solid var(--line);border-radius:14px;background:rgba(67,194,229,.035);color:#7f9ba5;font-size:10px;white-space:nowrap}.slider-header-actions .profile-count strong{font-size:16px;color:#eaf8fb}
.slider-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.slider-card{position:relative;display:grid;grid-template-columns:30px 170px minmax(0,1fr) 68px;min-height:170px;overflow:hidden;border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,37,50,.9),rgba(3,19,27,.94));transition:transform .2s,border-color .2s,box-shadow .2s}.slider-card:hover{transform:translateY(-2px);border-color:rgba(72,216,241,.38)}.slider-card.dragging{opacity:.55;transform:scale(.985)}.slider-card.drag-over{border-color:rgba(72,216,241,.7);box-shadow:0 0 0 2px rgba(72,216,241,.12)}.drag-handle{display:grid;place-items:center;color:#668994;cursor:grab;background:rgba(72,216,241,.025);border-right:1px solid rgba(116,221,239,.08);touch-action:none}.drag-handle:active{cursor:grabbing}.slider-media{display:block;width:170px;height:170px;overflow:hidden;background:#061923}.slider-media img{width:100%;height:100%;display:block;object-fit:cover}.slider-content{padding:15px 13px;min-width:0;color:inherit;text-decoration:none}.slider-top{display:flex;align-items:center;justify-content:space-between;gap:8px}.status{display:inline-flex;align-items:center;gap:6px;padding:5px 8px;border-radius:999px;font-size:8px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.status i{width:5px;height:5px;border-radius:50%;background:currentColor}.status.live{color:#8de8cc;background:rgba(67,194,137,.1)}.status.scheduled{color:#8bd9ff;background:rgba(67,176,229,.1)}.status.draft{color:#ffc77d;background:rgba(255,183,77,.1)}.status.expired{color:#ff9eaa;background:rgba(255,93,113,.1)}.slider-content h2{font-size:16px;line-height:1.3;margin:10px 0 9px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.meta-row{display:flex;flex-direction:column;gap:6px;color:#7898a2;font-size:9px;line-height:1.45}.meta-row span{display:flex;align-items:flex-start;gap:6px}.meta-row i{color:#58cfe7;width:11px;margin-top:1px}.slider-actions{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border-left:1px solid rgba(116,221,239,.1);padding:8px}.slider-actions form{margin:0}.toggle-btn,.delete-btn{width:36px;height:36px;border:1px solid;cursor:pointer;display:grid;place-items:center;border-radius:10px}.toggle-btn{padding:0;background:rgba(72,216,241,.06);border-color:rgba(72,216,241,.18)}.toggle-btn.active{background:rgba(67,194,137,.08);border-color:rgba(67,194,137,.24)}.toggle-btn.inactive{opacity:.7}.toggle-track{position:relative;width:20px;height:12px;border-radius:999px;background:#405962;display:block}.toggle-knob{position:absolute;top:2px;left:2px;width:8px;height:8px;border-radius:50%;background:#b3c4c8;transition:left .18s ease}.toggle-btn.active .toggle-track{background:#32b985}.toggle-btn.active .toggle-knob{left:10px;background:#effff8}.delete-btn{padding:0;background:rgba(255,93,113,.045);border-color:rgba(255,93,113,.14);color:#ff9eaa;font-size:13px}.save-status{min-height:0;margin:0 0 10px;color:#73dcbf;font-size:9px;text-align:right}.save-status:empty{display:none}.empty-state{grid-column:1/-1;display:flex;align-items:center;gap:14px;padding:18px 20px;border:1px solid var(--line);border-radius:16px;background:rgba(8,37,50,.38);color:#7898a2}.empty-icon{width:42px;height:42px;flex:0 0 42px;display:grid;place-items:center;border-radius:12px;background:rgba(72,216,241,.08);color:#58cfe7;font-size:18px}.empty-copy{display:flex;flex-direction:column;gap:3px}.empty-copy strong{color:var(--text);font-size:14px}.empty-copy span{font-size:9px;line-height:1.45}@media(max-width:900px){.slider-list{grid-template-columns:1fr}}@media(max-width:600px){.slider-card{grid-template-columns:26px 92px minmax(0,1fr) 58px;min-height:92px;border-radius:14px}.slider-media{width:92px;height:92px}.slider-content{padding:9px 9px}.slider-content h2{font-size:13px;margin:6px 0 5px}.meta-row{font-size:8px;gap:4px}.slider-actions{padding:5px}.drag-handle{font-size:11px}.slider-header-actions .profile-count{min-height:44px;padding:0 12px}}
</style>
@endpush
@push('scripts')
<script>
(() => {
    const list = document.getElementById('slider-list');
    if (!list) return;
    let dragged = null;

    list.querySelectorAll('.slider-card').forEach(card => {
        card.addEventListener('dragstart', e => {
            dragged = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.dataset.id);
        });
        card.addEventListener('dragend', async () => {
            card.classList.remove('dragging');
            list.querySelectorAll('.slider-card').forEach(c => c.classList.remove('drag-over'));
            if (!dragged) return;
            const ids = [...list.querySelectorAll('.slider-card')].map(c => Number(c.dataset.id));
            const original = ids.join(',');
            try {
                const response = await fetch('{{ route('admin.sliders.reorder') }}', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    body: JSON.stringify({order: ids})
                });
                if (!response.ok) throw new Error('save failed');
                const status = document.getElementById('slider-save-status');
                status.textContent = 'Order saved';
                setTimeout(() => status.textContent = '', 1600);
            } catch (error) {
                location.reload();
            }
            dragged = null;
        });
        card.addEventListener('dragover', e => {
            e.preventDefault();
            if (!dragged || dragged === card) return;
            card.classList.add('drag-over');
            const rect = card.getBoundingClientRect();
            const before = e.clientY < rect.top + rect.height / 2;
            if (before) list.insertBefore(dragged, card);
            else list.insertBefore(dragged, card.nextSibling);
        });
        card.addEventListener('dragleave', () => card.classList.remove('drag-over'));
        card.addEventListener('drop', e => {
            e.preventDefault();
            card.classList.remove('drag-over');
        });
    });
})();
</script>
@endpush