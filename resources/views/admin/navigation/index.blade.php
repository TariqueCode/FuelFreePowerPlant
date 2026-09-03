@extends('layouts.portal')
@section('title','Menu Builder')
@section('content')
<section class="hero">
    <div class="eyebrow">WEBSITE / NAVIGATION</div>
    <h1>Advanced Menu Builder</h1>
    <p>Build navigation from live website and dashboard sources. Nothing is invented here: only eligible, real application destinations can be added.</p>
</section>

@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="notice error-notice">{{ $errors->first() }}</div>@endif

<div class="mode-tabs" role="tablist" aria-label="Navigation area">
    <a class="{{ $area === 'public' ? 'active' : '' }}" href="{{ route('admin.navigation.index', ['menu' => 'main']) }}">🌐 Website</a>
    <a class="{{ $area === 'dashboard' ? 'active' : '' }}" href="{{ route('admin.navigation.index', ['menu' => 'dashboard']) }}">⚙ Dashboard</a>
</div>

<div class="quick-guide">
    <div><i class="fa-solid fa-wand-magic-sparkles"></i><strong>Live sources</strong><span>Routes and published CMS pages are discovered from the running application.</span></div>
    <div><i class="fa-solid fa-shield-halved"></i><strong>Permission aware</strong><span>Dashboard sources retain their route permission requirement.</span></div>
    <div><i class="fa-solid fa-arrows-up-down-left-right"></i><strong>Drag & drop</strong><span>Reorder, nest into folders, collapse branches, and save without reloading.</span></div>
</div>

<div class="builder-grid">
    <div class="builder-card structure-card">
        <div class="card-head"><strong>Menu Structure</strong><span id="save-state">Ready</span></div>
        <div class="simple-tip"><i class="fa-solid fa-circle-info"></i><span>Drag a row to reorder it. Hold <strong>Alt</strong> while dropping on a Folder to nest it. Pages and live sources can never become parents.</span></div>
        <div id="menu-tree">
            @forelse($items as $item)
                @include('admin.navigation._item', ['item' => $item])
            @empty
                <div class="empty"><i class="fa-regular fa-folder-open"></i><strong>No items in this menu yet.</strong><span>Choose a live source or create a folder from the panel.</span></div>
            @endforelse
        </div>
    </div>

    <div class="builder-card source-card">
        <div class="card-head"><strong>Add Navigation</strong><span>{{ $sources->count() }} live sources available</span></div>

        <div class="add-mode">
            <button type="button" class="add-mode-btn active" data-mode="source">Live Source</button>
            <button type="button" class="add-mode-btn" data-mode="folder">Folder</button>
        </div>

        <form method="POST" action="{{ route('admin.navigation.store') }}" class="builder-form" id="add-form">
            @csrf
            <input type="hidden" name="menu" value="{{ $menu }}">
            <input type="hidden" name="kind" id="kind" value="source">
            <input type="hidden" name="source_key" id="source-key">

            <div id="source-fields">
                <label>Choose a live destination
                    <select id="source-select">
                        <option value="">Select from the live application…</option>
                        @foreach($sources as $source)
                            <option value="{{ $source['key'] }}" data-label="{{ $source['label'] }}" data-url="{{ $source['url'] }}" data-permission="{{ $source['permission'] ?? '' }}">
                                {{ $source['label'] }} · {{ strtoupper($source['type']) }}@if($source['permission']) · {{ $source['permission'] }}@endif
                            </option>
                        @endforeach
                    </select>
                </label>
                <div id="source-preview" class="source-preview">Select an item to preview its destination.</div>
            </div>

            <div id="folder-fields" class="is-hidden">
                <label>Folder name
                    <input name="label" id="folder-label" maxlength="160" placeholder="Company">
                </label>
            </div>

            <label>Put inside
                <select name="parent_id">
                    <option value="">Top-level</option>
                    @foreach($all as $p)
                        @if($p->source_type === 'folder')
                            <option value="{{ $p->id }}">{{ str_repeat('↳ ', min(5,$p->depth ?? 0)) }}{{ $p->displayLabel() }}</option>
                        @endif
                    @endforeach
                </select>
            </label>

            <input type="hidden" name="label" id="source-label">
            <input type="hidden" name="target" value="_self">
            <input type="hidden" name="icon" value="">
            <label class="check"><input type="checkbox" name="is_visible" value="1" checked> Show in menu</label>
            <button class="primary" type="submit" id="add-button" disabled><i class="fa-solid fa-plus"></i> Add to menu</button>
        </form>
    </div>
</div>

<div class="builder-card full-card">
    <div class="card-head"><strong>Current Items</strong><span>Live sources are read-only destinations; folders are editable.</span></div>
    <div class="edit-grid">
        @foreach($all as $item)
            <details class="edit-details" id="edit-{{ $item->id }}">
                <summary>
                    <span><strong>{{ $item->displayLabel() }}</strong><small>{{ $item->source_type === 'folder' ? 'Folder' : 'Live source' }} · {{ $item->is_visible ? 'Visible' : 'Hidden' }}</small></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </summary>
                <div class="edit-content">
                    <form method="POST" action="{{ route('admin.navigation.update',$item) }}" class="edit-box">
                        @csrf @method('PATCH')
                        @if($item->source_type === 'folder')
                            <label>Folder name<input name="label" value="{{ $item->label }}" required maxlength="160"></label>
                        @else
                            <div class="locked-source"><i class="fa-solid fa-link"></i><span><strong>{{ $item->displayLabel() }}</strong><small>{{ $item->route_name ?: $item->url }}@if($item->permission_key) · {{ $item->permission_key }}@endif</small></span><b>LIVE</b></div>
                            <input type="hidden" name="label" value="{{ $item->label }}">
                        @endif
                        <label>Put inside
                            <select name="parent_id">
                                <option value="">Top-level</option>
                                @foreach($all as $p)
                                    @if($p->id !== $item->id && $p->source_type === 'folder')
                                        <option value="{{ $p->id }}" @selected($item->parent_id === $p->id)>{{ str_repeat('↳ ', min(5,$p->depth ?? 0)) }}{{ $p->displayLabel() }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </label>
                        <input type="hidden" name="target" value="{{ $item->target }}">
                        <input type="hidden" name="icon" value="{{ $item->icon }}">
                        <label class="check"><input type="checkbox" name="is_visible" value="1" @checked($item->is_visible)> Show in menu</label>
                        <button class="primary" type="submit">Save changes</button>
                    </form>
                    <form method="POST" action="{{ route('admin.navigation.destroy',$item) }}" onsubmit="return confirm('Delete this navigation item? Children will move to its parent.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="danger">Delete item</button>
                    </form>
                </div>
            </details>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
.mode-tabs{display:flex;gap:8px;margin:0 0 16px}.mode-tabs a{padding:10px 14px;border:1px solid var(--line);border-radius:11px;color:#7899a5;text-decoration:none;background:rgba(255,255,255,.02);font-size:11px;font-weight:700}.mode-tabs a.active{color:#e9fbff;border-color:rgba(98,217,238,.45);background:rgba(98,217,238,.08)}.quick-guide{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-bottom:16px}.quick-guide>div{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border:1px solid var(--line);border-radius:12px;background:rgba(255,255,255,.02)}.quick-guide i{color:#62d9ee;font-size:15px}.quick-guide strong{display:block;color:#dceff3;font-size:10px}.quick-guide span{color:#718f9a;font-size:9px;line-height:1.5;margin-left:auto}.builder-grid{display:grid;grid-template-columns:1.35fr .65fr;gap:16px}.builder-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:18px}.full-card{margin-top:16px}.card-head{display:flex;justify-content:space-between;gap:10px;margin-bottom:14px;color:#d9edf2}.card-head span{font-size:10px;color:#6e8d9a}.simple-tip{display:flex;gap:10px;align-items:flex-start;padding:11px 12px;margin-bottom:12px;border:1px solid var(--line);border-radius:11px;background:rgba(67,194,229,.035);color:#7899a5;font-size:10px;line-height:1.5}.simple-tip i{color:#62d9ee;margin-top:2px}.simple-tip strong{color:#dff7fb}.add-mode{display:grid;grid-template-columns:1fr 1fr;gap:6px;padding:4px;background:rgba(255,255,255,.025);border-radius:10px;margin-bottom:12px}.add-mode-btn{border:0;background:transparent;color:#76939e;border-radius:8px;padding:9px;font-size:10px;font-weight:700;cursor:pointer}.add-mode-btn.active{background:rgba(98,217,238,.1);color:#e7fbff}.builder-form,.edit-grid,.edit-box{display:grid;gap:10px}.builder-form label,.edit-box label{font-size:10px;color:#88a6b1}.builder-form input,.builder-form select,.edit-box input,.edit-box select{width:100%;box-sizing:border-box;margin-top:5px;padding:10px;border-radius:9px;border:1px solid var(--line);background:#071b27;color:#e8f6fa}.primary,.danger{border:0;border-radius:9px;padding:10px 13px;background:#31afd2;color:#fff;font-weight:700;cursor:pointer}.primary:disabled{opacity:.4;cursor:not-allowed}.danger{background:transparent;border:1px solid rgba(255,100,100,.25);color:#ff9a9a;font-size:10px}.check{display:flex!important;align-items:center;gap:7px}.check input{width:auto!important;margin:0!important}.source-preview,.locked-source{display:flex;gap:9px;align-items:center;padding:10px;border:1px solid var(--line);border-radius:9px;background:rgba(255,255,255,.018);color:#83a2ad;font-size:9px;line-height:1.4}.source-preview strong,.locked-source strong{color:#dff7fb}.locked-source span{flex:1}.locked-source small{display:block;color:#718f9a;margin-top:2px}.locked-source b{font-size:8px;color:#62d9ee;border:1px solid rgba(98,217,238,.25);padding:3px 5px;border-radius:999px}.is-hidden{display:none!important}.edit-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.edit-details{border:1px solid var(--line);border-radius:12px;background:rgba(255,255,255,.018);overflow:hidden}.edit-details summary{list-style:none;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 13px;cursor:pointer;color:#dff7fb}.edit-details summary::-webkit-details-marker{display:none}.edit-details summary small{display:block;color:#6f909d;font-size:9px;margin-top:3px}.edit-details[open] summary{border-bottom:1px solid var(--line)}.edit-details[open] summary>i{transform:rotate(180deg)}.edit-details summary>i{color:#6f909d;font-size:10px}.edit-content{padding:12px;display:grid;gap:10px}.edit-box{padding:13px;border:1px solid var(--line);border-radius:12px}.empty{text-align:center;color:#708d99;padding:36px;display:grid;gap:5px}.empty i{font-size:22px;color:#62d9ee}.empty strong{color:#dceff3}.menu-row{position:relative;display:grid;grid-template-columns:22px 28px minmax(0,1fr) auto;align-items:start;gap:4px;padding:5px;border-radius:10px;transition:background .15s}.menu-row>.children{grid-column:3 / -1;width:100%}.menu-row[data-kind="folder"]>.drag{color:#62d9ee}.tree-toggle,.tree-toggle-spacer{width:22px;height:22px;padding:0;border:0;background:transparent;color:#7eddeb;display:grid;place-items:center}.tree-toggle{cursor:pointer;border-radius:6px}.tree-toggle:hover{background:rgba(67,209,240,.08)}.menu-row.is-collapsed>.children{display:none}.menu-row.is-collapsed>.tree-toggle{transform:rotate(-90deg)}.drag{color:#6e8d9a;cursor:grab;user-select:none;display:grid;place-items:center;height:22px}.drag:active{cursor:grabbing}.menu-row.dragging{opacity:.45}.menu-row.drop-target{outline:1px dashed #62d9ee;background:rgba(98,217,238,.07)}.menu-main em{display:inline-block;margin-left:6px;font-style:normal;font-size:8px;color:#58cde4;border:1px solid rgba(88,205,228,.18);padding:2px 5px;border-radius:999px}.menu-main small{display:block;color:#6e8d9a;font-size:8px;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.item-actions{display:flex;gap:4px;align-items:center}.item-actions button,.item-actions a{border:1px solid var(--line);background:transparent;color:#7899a5;border-radius:7px;padding:5px 7px;font-size:9px;text-decoration:none;cursor:pointer}.item-actions button:hover,.item-actions a:hover{color:#dff7fb;border-color:rgba(98,217,238,.35)}.notice{padding:12px;border-radius:11px;margin-bottom:14px;background:rgba(67,194,137,.1);color:#a8e5ca}.error-notice{background:rgba(255,100,100,.1);color:#ffb0b0}@media(max-width:900px){.builder-grid,.edit-grid{grid-template-columns:1fr}.quick-guide{grid-template-columns:1fr}.quick-guide span{margin-left:0}}@media(max-width:620px){.builder-card{padding:13px}.item-actions .move-btn{display:block}.menu-row{grid-template-columns:22px 24px minmax(0,1fr)}.item-actions{grid-column:3;justify-content:flex-end}.menu-row>.children{grid-column:3 / -1}.mode-tabs a{flex:1;text-align:center}}
</style>
@endpush

@push('scripts')
<script>
(() => {
    const modeButtons = [...document.querySelectorAll('.add-mode-btn')];
    const kind = document.getElementById('kind');
    const sourceFields = document.getElementById('source-fields');
    const folderFields = document.getElementById('folder-fields');
    const sourceSelect = document.getElementById('source-select');
    const sourceKey = document.getElementById('source-key');
    const sourceLabel = document.getElementById('source-label');
    const preview = document.getElementById('source-preview');
    const addButton = document.getElementById('add-button');
    const folderLabel = document.getElementById('folder-label');

    const sync = (mode) => {
        kind.value = mode;
        sourceFields.classList.toggle('is-hidden', mode !== 'source');
        folderFields.classList.toggle('is-hidden', mode !== 'folder');
        addButton.disabled = mode === 'source' ? !sourceSelect.value : !folderLabel.value.trim();
    };

    modeButtons.forEach(btn => btn.addEventListener('click', () => {
        modeButtons.forEach(b => b.classList.toggle('active', b === btn));
        sync(btn.dataset.mode);
    }));

    sourceSelect?.addEventListener('change', () => {
        const option = sourceSelect.selectedOptions[0];
        sourceKey.value = option?.value || '';
        sourceLabel.value = option?.dataset.label || '';
        preview.innerHTML = option?.value
            ? '<strong>' + option.dataset.label + '</strong><span>↗ ' + option.dataset.url + (option.dataset.permission ? ' · ' + option.dataset.permission : '') + '</span>'
            : 'Select an item to preview its destination.';
        addButton.disabled = !sourceSelect.value;
    });

    folderLabel?.addEventListener('input', () => { addButton.disabled = !folderLabel.value.trim(); });

    const tree = document.getElementById('menu-tree');
    const state = document.getElementById('save-state');
    const originalTree = tree?.innerHTML;
    if (!tree) return;
    let dragged = null;

    const persist = async (container) => {
        const rows = [...container.children].filter(el => el.matches('.menu-row[data-id]'));
        const ids = rows.map(el => Number(el.dataset.id));
        const parentRow = container.closest('.menu-row[data-id]');
        state.textContent = 'Saving…';
        try {
            const response = await fetch('{{ route('admin.navigation.reorder') }}', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                body: JSON.stringify({menu:'{{ $menu }}', ids, parent_id: parentRow ? Number(parentRow.dataset.id) : null})
            });
            if (!response.ok) throw new Error('reorder failed');
            state.textContent = 'Saved';
        } catch (error) {
            state.textContent = 'Save failed — changes were not saved';
            window.setTimeout(() => window.location.reload(), 900);
        }
    };

    tree.addEventListener('click', async (event) => {
        const button = event.target.closest('.move-btn');
        if (!button) return;
        const row = button.closest('.menu-row[data-id]');
        const container = row?.parentElement;
        if (!row || !container) return;
        const siblings = [...container.children].filter(el => el.matches('.menu-row[data-id]'));
        const index = siblings.indexOf(row);
        const target = siblings[index + (button.classList.contains('move-up') ? -1 : 1)];
        if (!target) return;
        if (button.classList.contains('move-up')) container.insertBefore(row, target);
        else container.insertBefore(target, row);
        await persist(container);
    });

    tree.addEventListener('click', (event) => {
        const button = event.target.closest('.tree-toggle');
        if (!button) return;
        const row = button.closest('.menu-row');
        if (!row || !row.querySelector(':scope > .children')) return;
        const collapsed = row.classList.toggle('is-collapsed');
        button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
    });

    tree.querySelectorAll('.menu-row[data-id]').forEach(row => {
        row.setAttribute('draggable', 'true');
        row.addEventListener('dragstart', () => { dragged = row; row.classList.add('dragging'); });
        row.addEventListener('dragover', event => {
            if (!dragged || dragged === row || dragged.contains(row)) return;
            event.preventDefault();
            row.classList.add('drop-target');
        });
        row.addEventListener('dragleave', () => row.classList.remove('drop-target'));
        row.addEventListener('drop', async event => {
            event.preventDefault();
            row.classList.remove('drop-target');
            if (!dragged || dragged === row || dragged.contains(row)) return;

            const intoFolder = row.dataset.kind === 'folder' && event.altKey;
            let container = row.parentElement;
            if (intoFolder) {
                let children = row.querySelector(':scope > .children');
                if (!children) { children = document.createElement('div'); children.className = 'children'; row.appendChild(children); }
                children.appendChild(dragged);
                container = children;
            } else {
                container.insertBefore(dragged, row);
            }
            await persist(container);
        });
        row.addEventListener('dragend', () => {
            row.classList.remove('dragging');
            tree.querySelectorAll('.drop-target').forEach(el => el.classList.remove('drop-target'));
            dragged = null;
        });
    });
})();
</script>
@endpush