@extends('layouts.portal')

@section('title', 'Slider')

@section('content')
<section class="hero slider-hero">
    <div class="slider-hero-copy">
        <span class="eyebrow">PUBLIC WEBSITE CONTROL</span>
        <h1>Homepage Slider</h1>
        <p>Manage the images shown on the homepage. Reorder them, control publishing, and set optional display dates.</p>
    </div>

    <div class="slider-header-actions">
        @if(auth()->user()->hasPermission('website.manage'))
            <a class="primary slider-add" href="{{ route('admin.sliders.create') }}">
                <i class="fa-solid fa-plus"></i>
                <span>Add slider</span>
            </a>
        @endif

        <div class="slider-stats" aria-label="Slider status summary">
            <span><strong>{{ $stats['total'] }}</strong><small>Total</small></span>
            <span><strong>{{ $stats['live'] }}</strong><small>Live</small></span>
            <span><strong>{{ $stats['scheduled'] }}</strong><small>Scheduled</small></span>
            <span><strong>{{ $stats['draft'] + $stats['expired'] }}</strong><small>Inactive</small></span>
        </div>
    </div>
</section>

@if(session('status'))
    <div class="notice slider-notice" role="status">{{ session('status') }}</div>
@endif
@if(session('error'))
    <div class="error-notice slider-notice" role="alert">{{ session('error') }}</div>
@endif

<div id="slider-save-status" class="save-status" aria-live="polite"></div>

<section class="slider-panel" aria-labelledby="slider-list-title">
    <div class="slider-panel-head">
        <div>
            <h2 id="slider-list-title">Homepage slides</h2>
            <p>Drag a card on desktop or use the move controls on smaller screens.</p>
        </div>
        <span class="slider-count">{{ $stats['total'] }} {{ \Illuminate\Support\Str::plural('slide', $stats['total']) }}</span>
    </div>

    <div class="slider-list" id="slider-list">
        @forelse($sliders as $slider)
            @php
                $now = now();
                $state = ! $slider->is_published
                    ? 'draft'
                    : (($slider->starts_at && $slider->starts_at->gt($now))
                        ? 'scheduled'
                        : (($slider->ends_at && $slider->ends_at->lt($now)) ? 'expired' : 'live'));
            @endphp

            <article class="slider-card" draggable="true" data-id="{{ $slider->id }}">
                <div class="drag-handle" title="Drag to reorder" aria-label="Drag to reorder" role="button" tabindex="0">
                    <i class="fa-solid fa-grip-vertical" aria-hidden="true"></i>
                </div>

                <a class="slider-media" href="{{ route('admin.sliders.edit', $slider) }}" aria-label="Edit {{ $slider->title ?: 'homepage slider' }}">
                    <img src="{{ asset('storage/'.$slider->image_path) }}" alt="{{ $slider->title ?: 'Homepage slider' }}" loading="lazy">
                </a>

                <a class="slider-content" href="{{ route('admin.sliders.edit', $slider) }}">
                    <div class="slider-top">
                        <span class="status {{ $state }}">
                            <i aria-hidden="true"></i>
                            {{ ucfirst($state) }}
                        </span>
                    </div>
                    <h3>{{ $slider->title ?: 'Untitled slider' }}</h3>
                    <div class="meta-row">
                        <span>
                            <i class="fa-solid fa-link" aria-hidden="true"></i>
                            {{ $slider->link_url ? 'Linked image' : 'No link' }}
                        </span>
                        <span>
                            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                            {{ $slider->starts_at?->format('d M Y, H:i') ?: 'Immediately' }}
                            {{ $slider->ends_at ? ' → '.$slider->ends_at->format('d M Y, H:i') : ' · No expiry' }}
                        </span>
                    </div>
                </a>

                <div class="slider-actions">
                    <div class="move-actions" aria-label="Move slide">
                        <button type="button" class="move-btn" data-move="up" title="Move up" aria-label="Move {{ $slider->title ?: 'slide' }} up">
                            <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="move-btn" data-move="down" title="Move down" aria-label="Move {{ $slider->title ?: 'slide' }} down">
                            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                        </button>
                    </div>

                    @if(auth()->user()->hasPermission('website.publish'))
                        <form method="POST" action="{{ route('admin.sliders.update', $slider) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="toggle" value="1">
                            <button class="toggle-btn {{ $slider->is_published ? 'active' : 'inactive' }}" type="submit"
                                title="{{ $slider->is_published ? 'Deactivate' : 'Activate' }}"
                                aria-label="{{ $slider->is_published ? 'Deactivate' : 'Activate' }} {{ $slider->title ?: 'slide' }}">
                                <span class="toggle-track" aria-hidden="true"><span class="toggle-knob"></span></span>
                            </button>
                        </form>
                    @endif

                    @if(auth()->user()->hasPermission('website.manage'))
                        <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}"
                            onsubmit="return confirm('Delete this slider image? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button class="delete-btn" type="submit" title="Delete slider" aria-label="Delete {{ $slider->title ?: 'slide' }}">
                                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="empty-state slider-empty">
                <div class="empty-icon"><i class="fa-regular fa-images" aria-hidden="true"></i></div>
                <div class="empty-copy">
                    <strong>No slider images yet</strong>
                    <span>Add your first company image. Published images will rotate automatically on the homepage.</span>
                    @if(auth()->user()->hasPermission('website.manage'))
                        <a class="primary" href="{{ route('admin.sliders.create') }}"><i class="fa-solid fa-plus"></i> Add your first slider</a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</section>
@endsection

@push('styles')
<style>
.slider-hero{display:grid;grid-template-columns:minmax(0,1fr) minmax(320px,520px);gap:24px;align-items:end;margin-bottom:20px;padding:26px 0 20px}.slider-hero-copy{min-width:0}.slider-hero-copy h1{margin:7px 0 10px}.slider-hero-copy p{max-width:760px;margin:0}.slider-header-actions{display:grid;grid-template-columns:minmax(150px,1fr) auto;gap:10px;align-items:stretch}.slider-add{min-height:48px}.slider-stats{display:grid;grid-template-columns:repeat(4,minmax(68px,1fr));gap:6px}.slider-stats span{min-width:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:7px 10px;border:1px solid var(--line);border-radius:12px;background:rgba(67,194,229,.035)}.slider-stats strong{font-size:16px;line-height:1.15;color:var(--text)}.slider-stats small{margin-top:3px;color:#7898a2;font-size:9px;font-weight:700;letter-spacing:.03em}.slider-notice{margin:0 0 14px}.save-status{min-height:0;margin:0 0 10px;color:#73dcbf;font-size:12px;text-align:right}.save-status:empty{display:none}.slider-panel{border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,37,50,.72),rgba(3,19,27,.86));overflow:hidden}.slider-panel-head{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 20px;border-bottom:1px solid rgba(116,221,239,.1)}.slider-panel-head h2{margin:0;font-size:18px}.slider-panel-head p{margin:4px 0 0;font-size:11px;color:#7898a2}.slider-count{flex:0 0 auto;padding:7px 10px;border:1px solid var(--line);border-radius:999px;color:#9db9bf;font-size:10px}.slider-list{display:grid;grid-template-columns:1fr;gap:10px;padding:12px}.slider-card{position:relative;display:grid;grid-template-columns:34px 220px minmax(0,1fr) 116px;min-height:190px;overflow:hidden;border:1px solid var(--line);border-radius:16px;background:linear-gradient(145deg,rgba(8,37,50,.9),rgba(3,19,27,.94));transition:transform .2s,border-color .2s,box-shadow .2s}.slider-card:hover{transform:translateY(-1px);border-color:rgba(72,216,241,.34);box-shadow:0 10px 28px rgba(0,0,0,.16)}.slider-card.dragging{opacity:.55;transform:scale(.985)}.slider-card.drag-over{border-color:rgba(72,216,241,.72);box-shadow:0 0 0 2px rgba(72,216,241,.12)}.drag-handle{display:grid;place-items:center;color:#668994;cursor:grab;background:rgba(72,216,241,.025);border-right:1px solid rgba(116,221,239,.08);touch-action:none}.drag-handle:active{cursor:grabbing}.slider-media{display:block;width:220px;height:190px;overflow:hidden;background:#061923}.slider-media img{width:100%;height:100%;display:block;object-fit:cover}.slider-content{padding:20px 22px;min-width:0;color:inherit;text-decoration:none;display:flex;flex-direction:column;justify-content:center}.slider-top{display:flex;align-items:center;gap:8px}.status{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.status i{width:6px;height:6px;border-radius:50%;background:currentColor}.status.live{color:#8de8cc;background:rgba(67,194,137,.1)}.status.scheduled{color:#8bd9ff;background:rgba(67,176,229,.1)}.status.draft{color:#ffc77d;background:rgba(255,183,77,.1)}.status.expired{color:#ff9eaa;background:rgba(255,93,113,.1)}.slider-content h3{font-size:20px;line-height:1.3;margin:11px 0 12px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.meta-row{display:flex;flex-wrap:wrap;gap:10px 20px;color:#7898a2;font-size:11px;line-height:1.45}.meta-row span{display:flex;align-items:flex-start;gap:6px;min-width:0}.meta-row i{color:#58cfe7;width:12px;margin-top:2px;flex:0 0 12px}.slider-actions{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border-left:1px solid rgba(116,221,239,.1);padding:10px}.slider-actions form{margin:0}.move-actions{display:flex;gap:6px}.move-btn,.toggle-btn,.delete-btn{width:40px;height:40px;border:1px solid;cursor:pointer;display:grid;place-items:center;border-radius:11px}.move-btn{background:rgba(72,216,241,.045);border-color:rgba(72,216,241,.12);color:#8eb5be}.move-btn:hover{background:rgba(72,216,241,.1);color:#dffaff}.toggle-btn{padding:0;background:rgba(72,216,241,.06);border-color:rgba(72,216,241,.18)}.toggle-btn.active{background:rgba(67,194,137,.08);border-color:rgba(67,194,137,.24)}.toggle-btn.inactive{opacity:.7}.toggle-track{position:relative;width:24px;height:14px;border-radius:999px;background:#405962;display:block}.toggle-knob{position:absolute;top:2px;left:2px;width:10px;height:10px;border-radius:50%;background:#b3c4c8;transition:left .18s ease}.toggle-btn.active .toggle-track{background:#32b985}.toggle-btn.active .toggle-knob{left:12px;background:#effff8}.delete-btn{padding:0;background:rgba(255,93,113,.045);border-color:rgba(255,93,113,.14);color:#ff9eaa}.delete-btn:hover{background:rgba(255,93,113,.1)}.move-btn:focus-visible,.toggle-btn:focus-visible,.delete-btn:focus-visible,.slider-media:focus-visible,.slider-content:focus-visible,.drag-handle:focus-visible{outline:2px solid #61d8f1;outline-offset:2px}.slider-empty{margin:0;padding:24px;display:flex;align-items:center;gap:14px;text-align:left}.empty-copy{align-items:flex-start}.empty-copy .primary{margin-top:9px}@media(max-width:1120px){.slider-hero{grid-template-columns:1fr}.slider-header-actions{grid-template-columns:minmax(180px,1fr) auto}.slider-card{grid-template-columns:30px 180px minmax(0,1fr) 112px;min-height:170px}.slider-media{width:180px;height:170px}.slider-content{padding:16px}.slider-content h3{font-size:18px}.meta-row{font-size:10px}}@media(max-width:760px){.slider-hero{padding-top:10px}.slider-header-actions{grid-template-columns:1fr}.slider-stats{grid-template-columns:repeat(4,1fr)}.slider-panel-head{align-items:flex-start;flex-direction:column;gap:9px}.slider-list{padding:10px}.slider-card{grid-template-columns:1fr;min-height:0}.drag-handle{position:absolute;z-index:2;top:10px;left:10px;width:34px;height:34px;border:1px solid rgba(116,221,239,.16);border-radius:10px;background:rgba(3,19,27,.76);backdrop-filter:blur(8px)}.slider-media{width:100%;height:auto;aspect-ratio:16/8;min-height:0}.slider-content{padding:14px}.slider-content h3{font-size:17px;margin:9px 0}.meta-row{font-size:10px;gap:7px 14px}.slider-actions{flex-direction:row;justify-content:flex-end;border-left:0;border-top:1px solid rgba(116,221,239,.1);padding:10px 12px}.move-actions{margin-right:auto}.slider-empty{align-items:flex-start}}@media(max-width:520px){.slider-hero{margin-bottom:14px;padding-bottom:14px}.slider-stats span{padding:7px 5px}.slider-stats strong{font-size:14px}.slider-stats small{font-size:8px}.slider-panel-head{padding:15px}.slider-panel-head h2{font-size:17px}.slider-panel-head p{font-size:10px}.slider-count{font-size:9px}.slider-list{padding:8px}.slider-card{border-radius:14px}.slider-media{aspect-ratio:16/9}.slider-content{padding:12px}.slider-content h3{font-size:16px}.meta-row{font-size:9px;flex-direction:column;gap:5px}.slider-actions{padding:9px}.move-btn,.toggle-btn,.delete-btn{width:42px;height:42px}.slider-empty{padding:18px 14px;flex-direction:column}}@media(max-width:380px){.slider-stats{grid-template-columns:repeat(2,1fr)}.slider-header-actions{gap:8px}}
</style>
@endpush

@push('scripts')
<script>
(() => {
    const list = document.getElementById('slider-list');
    const status = document.getElementById('slider-save-status');
    if (!list) return;

    let dragged = null;
    let saving = false;

    const cards = () => [...list.querySelectorAll('.slider-card')];
    const order = () => cards().map(card => Number(card.dataset.id));

    const showStatus = (message, error = false) => {
        if (!status) return;
        status.textContent = message;
        status.style.color = error ? '#ff9eaa' : '#73dcbf';
        if (message) window.setTimeout(() => {
            if (status.textContent === message) status.textContent = '';
        }, 1800);
    };

    const saveOrder = async () => {
        if (saving) return;
        saving = true;
        list.setAttribute('aria-busy', 'true');
        showStatus('Saving order…');

        try {
            const response = await fetch('{{ route('admin.sliders.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order() })
            });

            if (!response.ok) throw new Error('save failed');
            showStatus('Order saved');
        } catch (error) {
            showStatus('Could not save order. Reloading…', true);
            window.setTimeout(() => window.location.reload(), 500);
        } finally {
            saving = false;
            list.removeAttribute('aria-busy');
        }
    };

    cards().forEach(card => {
        card.addEventListener('dragstart', event => {
            dragged = card;
            card.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.dataset.id);
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            cards().forEach(item => item.classList.remove('drag-over'));
            if (dragged) saveOrder();
            dragged = null;
        });

        card.addEventListener('dragover', event => {
            event.preventDefault();
            if (!dragged || dragged === card) return;

            card.classList.add('drag-over');
            const rect = card.getBoundingClientRect();
            const before = event.clientY < rect.top + rect.height / 2;
            list.insertBefore(dragged, before ? card : card.nextSibling);
        });

        card.addEventListener('dragleave', () => card.classList.remove('drag-over'));
        card.addEventListener('drop', event => {
            event.preventDefault();
            card.classList.remove('drag-over');
        });
    });

    list.addEventListener('click', event => {
        const button = event.target.closest('.move-btn');
        if (!button) return;

        const card = button.closest('.slider-card');
        if (!card) return;

        const sibling = button.dataset.move === 'up' ? card.previousElementSibling : card.nextElementSibling;
        if (!sibling || !sibling.classList.contains('slider-card')) return;

        if (button.dataset.move === 'up') list.insertBefore(card, sibling);
        else list.insertBefore(sibling, card);

        saveOrder();
    });
})();
</script>
@endpush
