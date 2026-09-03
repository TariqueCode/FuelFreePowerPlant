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
    </div>
    <div class="item-actions">
        <button type="button" class="move-btn move-up" data-id="{{ $item->id }}" title="Move up" aria-label="Move {{ $item->displayLabel() }} up">↑</button>
        <button type="button" class="move-btn move-down" data-id="{{ $item->id }}" title="Move down" aria-label="Move {{ $item->displayLabel() }} down">↓</button>
        <a href="#edit-{{ $item->id }}">Edit</a>
    </div>
    @if($item->children->isNotEmpty())
        <div class="children">
            @foreach($item->children as $child)
                @include('admin.navigation._item', ['item' => $child])
            @endforeach
        </div>
    @endif
</div>