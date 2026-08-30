@extends('layouts.portal')
@section('title','Menu Builder')
@section('content')
<section class="hero">
    <div class="eyebrow">WEBSITE / NAVIGATION</div>
    <h1>Menu Builder</h1>
    <p>Organize pages and links into nested folders. Drag items to reorder or use the parent selector for precise placement.</p>
</section>

@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="notice error-notice">{{ $errors->first() }}</div>@endif

<div class="builder-grid">
    <div class="builder-card">
        <div class="card-head"><strong>Main Menu</strong><span>Nested hierarchy supported</span></div>
        <div id="menu-tree">
            @forelse($items as $item)
                @include('admin.navigation._item', ['item' => $item])
            @empty
                <div class="empty">No menu items yet.</div>
            @endforelse
        </div>
    </div>

    <div class="builder-card">
        <div class="card-head"><strong>Add Item</strong><span>Page or custom link</span></div>
        <form method="POST" action="{{ route('admin.navigation.store') }}" class="builder-form">
            @csrf
            <input type="hidden" name="menu" value="{{ $menu }}">
            <label>Type
                <select id="item-kind" name="kind">
                    <option value="link">Page / Link</option>
                    <option value="folder">Folder</option>
                </select>
            </label>
            <label>Label<input name="label" required maxlength="160" placeholder="About Us"></label>
            <label id="page-link-wrap">Page / custom URL
                <select id="page-link">
                    <option value="">Custom URL</option>
                    @foreach($pages as $page)
                        <option value="/pages/{{ $page->slug }}">{{ $page->title }} @unless($page->is_published)(Draft)@endunless</option>
                    @endforeach
                </select>
            </label>
            <label id="url-wrap">URL<input id="item-url" name="url" maxlength="500" placeholder="/about-us"></label>
            <label id="route-wrap">Route name<input name="route_name" maxlength="160" placeholder="site.about"></label>
            <label>Parent
                <select name="parent_id">
                    <option value="">Top-level</option>
                    @foreach($all as $p)
                        <option value="{{ $p->id }}">{{ str_repeat('↳ ', $p->depth ?? 0) }}{{ $p->label }}</option>
                    @endforeach
                </select>
            </label>
            <label>Menu Group<input name="group" maxlength="100" placeholder="Company"></label>
            <label>Target<select name="target"><option value="_self">Same window</option><option value="_blank">New window</option></select></label>
            <label>Icon<input name="icon" maxlength="100" placeholder="fa-solid fa-house"></label>
            <label class="check"><input type="checkbox" name="is_visible" value="1" checked> Show in menu</label>
            <button class="primary" type="submit"><i class="fa-solid fa-plus"></i> Add menu item</button>
        </form>
    </div>
</div>

<div class="builder-card full-card">
    <div class="card-head"><strong>Edit Existing Items</strong><span>Visibility, destination and hierarchy</span></div>
    <div class="edit-grid">
        @foreach($all as $item)
            <form id="edit-{{ $item->id }}" method="POST" action="{{ route('admin.navigation.update',$item) }}" class="edit-box">
                @csrf @method('PATCH')
                <div class="edit-title"><strong>{{ $item->label }}</strong><button type="submit">Save</button></div>
                <input name="label" value="{{ $item->label }}" required maxlength="160">
                <input name="url" value="{{ $item->url }}" maxlength="500" placeholder="/url">
                <input name="route_name" value="{{ $item->route_name }}" maxlength="160" placeholder="route.name">
                <select name="parent_id">
                    <option value="">Top-level</option>
                    @foreach($all as $p)
                        @if($p->id !== $item->id)
                            <option value="{{ $p->id }}" @selected($item->parent_id === $p->id)>{{ str_repeat('↳ ', $p->depth ?? 0) }}{{ $p->label }}</option>
                        @endif
                    @endforeach
                </select>
                <label class="check"><input type="checkbox" name="is_visible" value="1" @checked($item->is_visible)> Visible</label>
                <input type="hidden" name="target" value="{{ $item->target }}">
                <input type="hidden" name="group" value="{{ $item->group }}">
                <input type="hidden" name="icon" value="{{ $item->icon }}">
            </form>
            <form method="POST" action="{{ route('admin.navigation.destroy',$item) }}" class="delete-form" onsubmit="return confirm('Delete this menu item? Its children will move to this item’s parent.');">
                @csrf @method('DELETE')
                <button type="submit" class="danger">Delete {{ $item->label }}</button>
            </form>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
.builder-grid{display:grid;grid-template-columns:1.3fr .7fr;gap:16px}.builder-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;padding:18px}.full-card{margin-top:16px}.card-head{display:flex;justify-content:space-between;gap:10px;margin-bottom:14px;color:#d9edf2}.card-head span{font-size:10px;color:#6e8d9a}.builder-form,.edit-grid{display:grid;gap:10px}.builder-form label,.edit-box label{font-size:10px;color:#88a6b1}.builder-form input,.builder-form select,.edit-box input,.edit-box select{width:100%;box-sizing:border-box;margin-top:5px;padding:10px;border-radius:9px;border:1px solid var(--line);background:#071b27;color:#e8f6fa}.check{display:flex!important;align-items:center;gap:7px}.check input{width:auto!important;margin:0!important}.primary,.edit-title button,.danger{border:0;border-radius:9px;padding:10px 13px;background:#31afd2;color:#fff;font-weight:700;cursor:pointer}.danger{background:transparent;border:1px solid rgba(255,100,100,.25);color:#ff9a9a;font-size:10px}.edit-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.edit-box{padding:13px;border:1px solid var(--line);border-radius:12px}.edit-title{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;color:#dceff3}.edit-title button{padding:7px 10px;font-size:10px}.is-hidden{display:none!important}.notice{padding:12px;border-radius:11px;margin-bottom:14px;background:rgba(67,194,137,.1);color:#a8e5ca}.error-notice{background:rgba(255,100,100,.1);color:#ffb0b0}.empty{text-align:center;color:#708d99;padding:28px}@media(max-width:800px){.builder-grid,.edit-grid{grid-template-columns:1fr}}
</style>
@endpush

@push('scripts')
<script>
const kind = document.getElementById('item-kind');
const pageLink = document.getElementById('page-link');
const urlWrap = document.getElementById('url-wrap');
const routeWrap = document.getElementById('route-wrap');
const pageWrap = document.getElementById('page-link-wrap');
const syncKind = () => {
    const folder = kind?.value === 'folder';
    [pageWrap, urlWrap, routeWrap].forEach(el => el?.classList.toggle('is-hidden', folder));
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
