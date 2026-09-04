@if(($item->depth ?? 0) === 0)
<style>
.inline-label-edit{margin-top:8px;padding:8px;border:1px solid var(--line);border-radius:9px;background:rgba(98,217,238,.035)}
.inline-label-edit form{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
.inline-label-edit input[name="label"]{flex:1 1 180px;min-width:0;margin:0;padding:7px 9px;border-radius:8px;border:1px solid var(--line);background:#071b27;color:#e8f6fa;font-size:10px}
.inline-label-edit input[name="label"]:focus{outline:none;border-color:rgba(98,217,238,.55);box-shadow:0 0 0 3px rgba(98,217,238,.08)}
.rename-save,.rename-cancel{border-radius:8px;padding:7px 10px;font-size:9px;font-weight:700;cursor:pointer}
.rename-save{border:0;background:#31afd2;color:#fff}.rename-cancel{border:1px solid var(--line);background:transparent;color:#88a6b1}
.rename-btn{border:1px solid rgba(98,217,238,.2);background:rgba(98,217,238,.045);color:#8fd8e7;border-radius:7px;padding:4px 7px;font-size:8px;cursor:pointer}
.rename-btn:hover{background:rgba(98,217,238,.09);color:#e8fbff;border-color:rgba(98,217,238,.35)}
@media(max-width:700px){.rename-btn{padding:5px 7px}.inline-label-edit{margin-top:7px}.inline-label-edit input[name="label"]{flex-basis:100%}}
</style>
@endif
<div class="menu-row" draggable="false" data-id="{{ $item->id }}" data-kind="{{ $item->source_type }}">
    @if($item->children->isNotEmpty())
        <button type="button" class="tree-toggle" aria-expanded="true" aria-label="Collapse {{ $item->displayLabel() }}">⌄</button>
    @else
        <span class="tree-toggle-spacer" aria-hidden="true"></span>
    @endif
    <span class="drag" title="Drag to reorder" aria-label="Drag to reorder" role="button" tabindex="0">☷</span>
    <div class="menu-main">
        <strong class="drag" title="Drag {{ $item->displayLabel() }} to reorder">{{ $item->displayLabel() }}</strong>
        <em>{{ $item->source_type === 'folder' ? 'Folder' : 'Live source' }}</em>
        <small>{{ $item->route_name ?: ($item->url ?: 'Structural node') }} · {{ $item->is_visible ? 'Visible' : 'Hidden' }}@if($item->permission_key) · {{ $item->permission_key }}@endif</small>
        <div class="inline-label-edit" id="rename-{{ $item->id }}" hidden>
            <form method="POST" action="{{ route('admin.navigation.update', $item) }}">
                @csrf @method('PATCH')
                <input name="label" value="{{ $item->displayLabel() }}" maxlength="160" required aria-label="Navigation label">
                <input type="hidden" name="parent_id" value="{{ $item->parent_id }}">
                <input type="hidden" name="target" value="{{ $item->target }}">
                <input type="hidden" name="icon" value="{{ $item->icon }}">
                <input type="hidden" name="is_visible" value="{{ $item->is_visible ? 1 : 0 }}">
                <button type="submit" class="rename-save">Save</button>
                <button type="button" class="rename-cancel" onclick="document.getElementById('rename-{{ $item->id }}').hidden=true">Cancel</button>
            </form>
        </div>
    </div>
    <div class="item-actions">
        <button type="button" class="move-btn move-up" data-id="{{ $item->id }}" title="Move up" aria-label="Move {{ $item->displayLabel() }} up">↑</button>
        <button type="button" class="move-btn move-down" data-id="{{ $item->id }}" title="Move down" aria-label="Move {{ $item->displayLabel() }} down">↓</button>
        <button type="button" class="rename-btn" onclick="document.getElementById('rename-{{ $item->id }}').hidden=false; document.getElementById('rename-{{ $item->id }}').querySelector('input[name=label]').focus();" title="Rename navigation item">Edit name</button>
    </div>
    @if($item->children->isNotEmpty())
        <div class="children">
            @foreach($item->children as $child)
                @include('admin.navigation._item', ['item' => $child])
            @endforeach
        </div>
    @endif
</div>