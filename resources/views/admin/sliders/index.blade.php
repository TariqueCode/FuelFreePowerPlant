@extends('layouts.portal')

@section('title', 'Slider')

@section('content')
<div class="slider-page">
    <header class="slider-page-head">
        <div>
            <span class="eyebrow">PUBLIC WEBSITE CONTROL</span>
            <h1>Homepage Slider</h1>
            <p>Manage the banner images shown on the homepage. Drag and drop cards to change their display order.</p>
        </div>

        @if(auth()->user()->hasPermission('website.manage'))
            <a class="primary slider-add" href="{{ route('admin.sliders.create') }}">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                <span>Add slider</span>
            </a>
        @endif
    </header>

    <div class="slider-summary" aria-label="Slider status summary">
        <div><strong>{{ $stats['total'] }}</strong><span>Total</span></div>
        <div><strong>{{ $stats['live'] }}</strong><span>Live</span></div>
        <div><strong>{{ $stats['scheduled'] }}</strong><span>Scheduled</span></div>
        <div><strong>{{ $stats['draft'] + $stats['expired'] }}</strong><span>Inactive</span></div>
    </div>

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
                <p>Drag a card to reorder. The order is saved automatically.</p>
            </div>
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
                        <span>Add your first company banner. Published images will rotate automatically on the homepage.</span>
                        @if(auth()->user()->hasPermission('website.manage'))
                            <a class="primary" href="{{ route('admin.sliders.create') }}"><i class="fa-solid fa-plus" aria-hidden="true"></i> Add your first slider</a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.slider-page{max-width:1280px;margin:0 auto;padding:26px 0 40px}.slider-page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:28px;margin-bottom:16px}.slider-page-head h1{margin:7px 0 9px;font-size:clamp(32px,3.4vw,48px);line-height:1.05}.slider-page-head p{max-width:760px;margin:0;color:#91adb5;font-size:15px;line-height:1.65}.slider-add{min-height:46px;flex:0 0 auto;white-space:nowrap;color:#9fe6f3}.slider-add:hover,.slider-add:focus-visible{color:#dffaff}.slider-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:18px}.slider-summary div{min-height:76px;display:flex;flex-direction:column;justify-content:center;padding:12px 16px;border:1px solid var(--line);border-radius:14px;background:rgba(67,194,229,.035)}.slider-summary strong{font-size:22px;line-height:1.1;color:var(--text)}.slider-summary span{margin-top:4px;color:#7898a2;font-size:10px;font-weight:800;letter-spacing:.05em;text-transform:uppercase}.slider-notice{margin:0 0 14px}.save-status{min-height:0;margin:0 0 9px;color:#73dcbf;font-size:12px;text-align:right}.save-status:empty{display:none}.slider-panel{border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,37,50,.72),rgba(3,19,27,.9));overflow:hidden}.slider-panel-head{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 20px;border-bottom:1px solid rgba(116,221,239,.1)}.slider-panel-head h2{margin:0;font-size:19px}.slider-panel-head p{margin:4px 0 0;font-size:11px;color:#7898a2}.slider-list{display:grid;grid-template-columns:1fr;gap:10px;padding:12px}.slider-card{position:relative;display:grid;grid-template-columns:38px minmax(300px,360px) minmax(0,1fr) 88px;min-height:158px;overflow:hidden;border:1px solid var(--line);border-radius:15px;background:linear-gradient(145deg,rgba(8,37,50,.92),rgba(3,19,27,.96));transition:transform .2s,border-color .2s,box-shadow .2s}.slider-card:hover{transform:translateY(-1px);border-color:rgba(72,216,241,.34);box-shadow:0 10px 28px rgba(0,0,0,.16)}.slider-card.dragging{opacity:.55;transform:scale(.988)}.slider-card.drag-over{border-color:rgba(72,216,241,.78);box-shadow:0 0 0 2px rgba(72,216,241,.12)}.drag-handle{display:grid;place-items:center;color:#668994;cursor:grab;background:rgba(72,216,241,.025);border-right:1px solid rgba(116,221,239,.08);touch-action:none}.drag-handle:active{cursor:grabbing}.slider-media{display:block;width:100%;height:100%;min-width:0;overflow:hidden;background:#061923}.slider-media img{display:block;width:100%;height:100%;object-fit:cover;object-position:center}.slider-content{min-width:0;padding:18px 20px;color:inherit;text-decoration:none;display:flex;flex-direction:column;justify-content:center}.slider-top{display:flex;align-items:center;gap:8px}.status{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.status i{width:6px;height:6px;border-radius:50%;background:currentColor}.status.live{color:#8de8cc;background:rgba(67,194,137,.1)}.status.scheduled{color:#8bd9ff;background:rgba(67,176,229,.1)}.status.draft{color:#ffc77d;background:rgba(255,183,77,.1)}.status.expired{color:#ff9eaa;background:rgba(255,93,113,.1)}.slider-content h3{font-size:20px;line-height:1.3;margin:10px 0 10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.meta-row{display:flex;flex-wrap:wrap;gap:8px 18px;color:#7898a2;font-size:11px;line-height:1.45}.meta-row span{display:flex;align-items:flex-start;gap:6px;min-width:0}.meta-row i{color:#58cfe7;width:12px;margin-top:2px;flex:0 0 12px}.slider-actions{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border-left:1px solid rgba(116,221,239,.1);padding:10px}.slider-actions form{margin:0}.toggle-btn,.delete-btn{width:40px;height:40px;border:1px solid;cursor:pointer;display:grid;place-items:center;border-radius:11px}.toggle-btn{padding:0;background:rgba(72,216,241,.06);border-color:rgba(72,216,241,.18)}.toggle-btn.active{background:rgba(67,194,137,.08);border-color:rgba(67,194,137,.24)}.toggle-btn.inactive{opacity:.7}.toggle-track{position:relative;width:24px;height:14px;border-radius:999px;background:#405962;display:block}.toggle-knob{position:absolute;top:2px;left:2px;width:10px;height:10px;border-radius:50%;background:#b3c4c8;transition:left .18s ease}.toggle-btn.active .toggle-track{background:#32b985}.toggle-btn.active .toggle-knob{left:12px;background:#effff8}.delete-btn{padding:0;background:rgba(255,93,113,.045);border-color:rgba(255,93,113,.14);color:#ff9eaa}.delete-btn:hover{background:rgba(255,93,113,.1)}.toggle-btn:focus-visible,.delete-btn:focus-visible,.slider-media:focus-visible,.slider-content:focus-visible,.drag-handle:focus-visible{outline:2px solid #61d8f1;outline-offset:2px}.slider-empty{margin:0;padding:28px 24px;display:flex;align-items:center;gap:14px;text-align:left}.empty-copy{align-items:flex-start}.empty-copy .primary{margin-top:9px}@media(max-width:1080px){.slider-page{padding-top:20px}.slider-page-head{align-items:flex-start;flex-direction:column;gap:16px}.slider-card{grid-template-columns:34px minmax(240px,300px) minmax(0,1fr) 78px;min-height:145px}.slider-content{padding:15px}.slider-content h3{font-size:18px}.meta-row{font-size:10px}}@media(max-width:760px){.slider-page{padding:12px 0 30px}.slider-page-head h1{font-size:32px}.slider-page-head p{font-size:13px;line-height:1.55}.slider-add{width:100%;justify-content:center}.slider-summary{grid-template-columns:repeat(4,minmax(0,1fr));gap:7px}.slider-summary div{min-height:68px;padding:9px 6px;text-align:center;align-items:center}.slider-summary strong{font-size:18px}.slider-summary span{font-size:8px}.slider-panel-head{align-items:flex-start;flex-direction:column;gap:9px;padding:15px}.slider-list{padding:9px}.slider-card{grid-template-columns:1fr;min-height:0}.drag-handle{position:absolute;z-index:3;top:10px;left:10px;width:34px;height:34px;border:1px solid rgba(116,221,239,.18);border-radius:10px;background:rgba(3,19,27,.78);backdrop-filter:blur(8px)}.slider-media{height:auto;aspect-ratio:16/6;min-height:0}.slider-content{padding:14px}.slider-content h3{font-size:17px;margin:9px 0}.meta-row{font-size:10px;gap:7px 14px}.slider-actions{flex-direction:row;justify-content:flex-end;border-left:0;border-top:1px solid rgba(116,221,239,.1);padding:9px 12px}.slider-empty{align-items:flex-start}}@media(max-width:520px){.slider-page-head h1{font-size:28px}.slider-page-head p{font-size:12px}.slider-summary{grid-template-columns:repeat(2,1fr)}.slider-summary div{min-height:64px}.slider-summary strong{font-size:17px}.slider-summary span{font-size:8px}.slider-panel-head h2{font-size:17px}.slider-panel-head p{font-size:10px}.slider-list{padding:8px}.slider-card{border-radius:13px}.slider-media{aspect-ratio:16/7}.slider-content{padding:12px}.slider-content h3{font-size:16px}.meta-row{font-size:9px;flex-direction:column;gap:5px}.toggle-btn,.delete-btn{width:42px;height:42px}.slider-empty{padding:18px 14px;flex-direction:column}}@media(max-width:380px){.slider-page-head h1{font-size:25px}}
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
})();
</script>
@endpush