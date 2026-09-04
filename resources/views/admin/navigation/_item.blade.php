<div class="menu-row" draggable="true" data-id="{{ $item->id }}" data-kind="{{ $item->source_type }}">
    @if($item->children->isNotEmpty())
        <button type="button" class="tree-toggle" aria-expanded="true" aria-label="Collapse {{ $item->displayLabel() }}">⌄</button>
    @else
        <span class="tree-toggle-spacer" aria-hidden="true"></span>
    @endif
    <span class="drag" title="Drag to reorder" aria-label="Drag to reorder" role="button">☷</span>
    <div class="menu-main">
        <strong>{{ $item->displayLabel() }}</strong>
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