@extends('layouts.portal')
@section('title','Menu Builder')
@section('content')
<section class="hero">
    <div class="eyebrow">WEBSITE / NAVIGATION</div>
    <h1>Menu Builder</h1>
    <p>Organize your website menu like folders: add a page or folder, then place it where visitors should find it.</p>
</section>

@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="notice error-notice">{{ $errors->first() }}</div>@endif

<div class="quick-guide"><div><i class="fa-solid fa-folder-tree"></i><strong>Simple menu rule</strong><span>Folder = dropdown. Page / Link = opens a destination. Drag to reorder.</span></div><div><i class="fa-solid fa-eye"></i><strong>Visibility</strong><span>Hide an item without deleting its content.</span></div></div>

<div class="builder-grid">
    <div class="builder-card">
        <div class="card-head"><strong>Menu Structure</strong><span>Folders open as dropdowns</span></div>
        <div class="simple-tip"><i class="fa-solid fa-hand-pointer"></i><span><strong>Move items</strong> Use ↑ / ↓ on phones. On desktop, drag an item or hold Shift while dropping it onto a folder to place it inside.</span></div>
        <div id="menu-tree">
            @forelse($items as $item)
                @include('admin.navigation._item', ['item' => $item])
            @empty
                <div class="empty">No menu items yet. Add your first page or folder.</div>
            @endforelse
        </div>
    </div>

    <div class="builder-card add-card">
        <div class="card-head"><strong>Add to Menu</strong><span>Only the essentials</span></div>
        <form method="POST" action="{{ route('admin.navigation.store') }}" class="builder-form">
            @csrf
            <input type="hidden" name="menu" value="{{ $menu }}">
            <label>Type
                <select id="item-kind" name="kind">
                    <option value="link">Page</option>
                    <option value="folder">Folder / Dropdown</option>
                </select>
            </label>
            <label>Name<input name="label" required maxlength="160" placeholder="Management Team"></label>
            <label id="page-link-wrap">Where should it open?
                <select id="page-link">
                    <option value="">Choose a website page</option>
                    <option value="{{ route('home') }}">Home</option>
                    <option value="{{ route('site.about') }}">About Us</option>
                    <option value="{{ route('management') }}">Management Team</option>
                    <option value="{{ route('site.plants') }}">Power Plants</option>
                    <option value="{{ route('site.future-project') }}">Future Projects</option>
                    <option value="{{ route('news.index') }}">News &amp; Events</option>
                    <option value="{{ route('site.gallery') }}">Gallery</option>
                    <option value="{{ route('site.career') }}">Career</option>
                    <option value="{{ route('sustainability') }}">Sustainability</option>
                    <option value="{{ route('contact') }}">Contact</option>
                    @foreach($pages as $page)
                        <option value="{{ route('cms.page',$page->slug) }}">{{ $page->title }} @unless($page->is_published)(Draft)@endunless</option>
                    @endforeach
                </select>
            </label>
            <input type="hidden" id="item-url" name="url">
            <input type="hidden" name="route_name" value="">
            <label>Put inside
                <select name="parent_id">
                    <option value="">Top-level</option>
                    @foreach($all as $p)
                        <option value="{{ $p->id }}">{{ str_repeat('↳ ', min(5,$p->depth ?? 0)) }}{{ $p->label }}</option>
                    @endforeach
                </select>
            </label>
            <input type="hidden" name="target" value="_self">
            <label class="check"><input type="checkbox" name="is_visible" value="1" checked> Show in menu</label>
            <button class="primary" type="submit"><i class="fa-solid fa-plus"></i> Add</button>
        </form>
    </div>
</div>

<div class="builder-card full-card">
    <div class="card-head"><strong>Edit items</strong><span>Open only the item you need</span></div>
    <div class="edit-grid">
        @foreach($all as $item)
            <details class="edit-details" id="edit-{{ $item->id }}">
                <summary><span><strong>{{ $item->label }}</strong><small>{{ $item->is_visible ? 'Visible' : 'Hidden' }} · {{ ($item->url || $item->route_name) ? 'Page' : 'Folder' }}</small></span><i class="fa-solid fa-chevron-down"></i></summary>
                <div class="edit-content">
                    <form method="POST" action="{{ route('admin.navigation.update',$item) }}" class="edit-box">
                        @csrf @method('PATCH')
                        <input type="hidden" name="kind" value="{{ ($item->url || $item->route_name) ? 'link' : 'folder' }}">
                        <label>Name<input name="label" value="{{ $item->label }}" required maxlength="160"></label>
                        <label>Page URL<input name="url" value="{{ $item->url }}" maxlength="500" placeholder="/page"></label>
                        <input type="hidden" name="route_name" value="{{ $item->route_name }}">
                        <label>Put inside
                            <select name="parent_id">
                                <option value="">Top-level</option>
                                @foreach($all as $p)
                                    @if($p->id !== $item->id)
                                        <option value="{{ $p->id }}" @selected($item->parent_id === $p->id)>{{ str_repeat('↳ ', min(5,$p->depth ?? 0)) }}{{ $p->label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </label>
                        <input type="hidden" name="target" value="{{ $item->target }}">
                        <input type="hidden" name="group" value="{{ $item->group }}">
                        <input type="hidden" name="icon" value="{{ $item->icon }}">
                        <label class="check"><input type="checkbox" name="is_visible" value="1" @checked($item->is_visible)> Show in menu</label>
                        <button class="primary" type="submit">Save changes</button>
                    </form>
                    <form method="POST" action="{{ route('admin.navigation.destroy',$item) }}" onsubmit="return confirm('Delete this menu item? Its children will move to this item’s parent.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="danger">Delete item</button>
                    </form>
                </div>
            </details>
        @endforeach
    </div>
</div>@endsection

@push('styles')
<style>
.simple-tip{display:flex;gap:10px;align-items:flex-start;padding:11px 12px;margin-bottom:12px;border:1px solid var(--line);border-radius:11px;background:rgba(67,194,229,.035);color:#7899a5;font-size:10px;line-height:1.5}.simple-tip i{color:#62d9ee;margin-top:2px}.simple-tip strong{color:#dff7fb}.edit-details{border:1px solid var(--line);border-radius:12px;background:rgba(255,255,255,.018);overflow:hidden}.edit-details summary{list-style:none;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 13px;cursor:pointer;color:#dff7fb}.edit-details summary::-webkit-details-marker{display:none}.edit-details summary small{display:block;color:#6f909d;font-size:9px;margin-top:3px}.edit-details[open] summary{border-bottom:1px solid var(--line)}.edit-details[open] summary>i{transform:rotate(180deg)}.edit-details summary>i{color:#6f909d;font-size:10px}.edit-content{padding:12px;display:grid;gap:10px}.edit-box{display:grid;gap:9px}.edit-box label{font-size:10px;color:#88a6b1}.edit-box input,.edit-box select{width:100%;box-sizing:border-box;margin-top:5px;padding:10px;border-radius:9px;border:1px solid var(--line);background:#071b27;color:#e8f6fa}.edit-box .primary{justify-self:start}.quick-guide>div{display:flex;align-items:center;gap:10px;padding:12px 14px;border:1px solid var(--line);border-radius:12px;background:rgba(255,255,255,.02)}.quick-guide i{color:#62d9ee;font-size:16px}.quick-guide strong{display:block;color:#dceff3;font-size:10px}.quick-guide span{color:#718f9a;font-size:9px;margin-left:auto}.builder-grid{display:grid;grid-template-columns:1.35fr .65fr;gap:16px}.builder-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:18px}.full-card{margin-top:16px}.card-head{display:flex;justify-content:space-between;gap:10px;margin-bottom:14px;color:#d9edf2}.card-head span{font-size:10px;color:#6e8d9a}.builder-form,.edit-grid{display:grid;gap:10px}.builder-form label,.edit-box label{font-size:10px;color:#88a6b1}.builder-form input,.builder-form select,.edit-box input,.edit-box select{width:100%;box-sizing:border-box;margin-top:5px;padding:10px;border-radius:9px;border:1px solid var(--line);background:#071b27;color:#e8f6fa}.check{display:flex!important;align-items:center;gap:7px}.check input{width:auto!important;margin:0!important}.primary,.edit-title button,.danger{border:0;border-radius:9px;padding:10px 13px;background:#31afd2;color:#fff;font-weight:700;cursor:pointer}.danger{background:transparent;border:1px solid rgba(255,100,100,.25);color:#ff9a9a;font-size:10px}.edit-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.edit-box{padding:13px;border:1px solid var(--line);border-radius:12px}.edit-title{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;color:#dceff3}.edit-title button{padding:7px 10px;font-size:10px}.is-hidden{display:none!important}.menu-row{position:relative;display:grid;grid-template-columns:22px 28px minmax(0,1fr) auto;align-items:start;gap:4px;padding:5px;border-radius:10px}.menu-row>.children{grid-column:3 / -1;width:100%}.tree-toggle,.tree-toggle-spacer{width:22px;height:22px;padding:0;border:0;background:transparent;color:#7eddeb;display:grid;place-items:center}.tree-toggle{cursor:pointer;border-radius:6px}.tree-toggle:hover{background:rgba(67,209,240,.08)}.menu-row.is-collapsed>.children{display:none}.menu-row.is-collapsed>.tree-toggle{transform:rotate(-90deg)}.menu-main em{display:inline-block;margin-left:6px;font-style:normal;font-size:8px;color:#58cde4;border:1px solid rgba(88,205,228,.18);padding:2px 5px;border-radius:999px}.quick-guide i{flex:0 0 auto}.notice{padding:12px;border-radius:11px;margin-bottom:14px;background:rgba(67,194,137,.1);color:#a8e5ca}.error-notice{background:rgba(255,100,100,.1);color:#ffb0b0}.empty{text-align:center;color:#708d99;padding:28px}@media(max-width:800px){.builder-grid,.edit-grid{grid-template-columns:1fr}.move-btn{display:block}.menu-row{grid-template-columns:22px 28px minmax(0,1fr) auto}.menu-row>.children{grid-column:3 / -1}.quick-guide>div{align-items:flex-start}.quick-guide span{margin-left:0}.builder-card{padding:14px}.card-head{align-items:flex-start;flex-direction:column;gap:4px}}
</style>
@endpush

@push('scripts')
<script>
const kind = document.getElementById('item-kind');
const pageLink = document.getElementById('page-link');
const urlWrap = null;
const routeWrap = null;
const pageWrap = document.getElementById('page-link-wrap');
const syncKind = () => {
    const folder = kind?.value === 'folder';
    pageWrap?.classList.toggle('is-hidden', folder);
    if (folder) {
        const url = document.getElementById('item-url');
        const route = document.querySelector('input[name="route_name"]');
        if (url) url.value = '';
        if (route) route.value = '';
    }
};
kind?.addEventListener('change', syncKind);
pageLink?.addEventListener('change', e => {
    const url = document.getElementById('item-url');
    if (url) url.value = e.target.value;
});
syncKind();

(() => {
    const tree = document.getElementById('menu-tree');
    if (!tree) return;

    let dragged = null;

    const rows = () => [...tree.querySelectorAll('.menu-row[data-id]')];

    tree.addEventListener('click', async e => {
        const btn=e.target.closest('.move-btn'); if(!btn)return;
        const row=btn.closest('.menu-row[data-id]'); if(!row)return;
        const parent=row.parentElement.closest('.menu-row[data-id]');
        const container=row.parentElement;
        const siblings=[...container.children].filter(el=>el.matches('.menu-row[data-id]'));
        const i=siblings.indexOf(row); const delta=btn.classList.contains('move-up')?-1:1; const target=siblings[i+delta];
        if(!target)return;
        if(delta<0)container.insertBefore(row,target); else container.insertBefore(target,row);
        const ids=[...container.children].filter(el=>el.matches('.menu-row[data-id]')).map(el=>Number(el.dataset.id));
        const response=await fetch('{{ route('admin.navigation.reorder') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:JSON.stringify({menu:'{{ $menu }}',ids,parent_id:parent?Number(parent.dataset.id):null})});
        if(!response.ok)window.location.reload();
    });

    tree.addEventListener('click', e => {
        const button = e.target.closest('.tree-toggle');
        if (!button) return;
        const row = button.closest('.menu-row');
        if (!row || !row.querySelector(':scope > .children')) return;
        const collapsed = row.classList.toggle('is-collapsed');
        button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        button.setAttribute('aria-label', (collapsed ? 'Expand ' : 'Collapse ') + (row.querySelector(':scope > .menu-main strong')?.textContent || 'folder'));
    });

    rows().forEach(row => {
        row.addEventListener('dragstart', () => {
            dragged = row;
            row.classList.add('dragging');
        });

        row.addEventListener('dragover', e => {
            e.preventDefault();
            if (!dragged || dragged === row || dragged.contains(row)) return;
            row.classList.add('drop-target');
        });

        row.addEventListener('dragleave', () => row.classList.remove('drop-target'));

        row.addEventListener('drop', async e => {
            e.preventDefault();
            row.classList.remove('drop-target');
            if (!dragged || dragged === row || dragged.contains(row)) return;

            const makeChild = e.altKey || e.shiftKey;
            let container = row.parentElement;

            if (makeChild) {
                let children = row.querySelector(':scope > .children');
                if (!children) {
                    children = document.createElement('div');
                    children.className = 'children';
                    row.appendChild(children);
                }
                children.appendChild(dragged);
                container = children;
            } else {
                container.insertBefore(dragged, row);
            }

            const parentRow = container.closest('.menu-row[data-id]');
            const ids = [...container.children]
                .filter(el => el.matches('.menu-row[data-id]'))
                .map(el => Number(el.dataset.id));

            try {
                const response = await fetch('{{ route('admin.navigation.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        menu: '{{ $menu }}',
                        ids,
                        parent_id: parentRow ? Number(parentRow.dataset.id) : null
                    })
                });

                if (!response.ok) throw new Error('reorder failed');
            } catch (error) {
                window.location.reload();
            }
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
