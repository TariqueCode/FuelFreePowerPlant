<div class="menu-row" draggable="true" data-id="{{ $item->id }}">
    @if($item->children->isNotEmpty())<button type="button" class="tree-toggle" aria-expanded="true" aria-label="Collapse {{ $item->label }}">⌄</button>@else<span class="tree-toggle-spacer" aria-hidden="true"></span>@endif<span class="drag" title="Drag to reorder" aria-label="Drag to reorder" role="button">☷</span>
    <div class="menu-main">
        <strong>{{ $item->label }}</strong>
        <em>{{ $item->children->isNotEmpty() ? "Folder" : "Page / Link" }}</em>
        <small>{{ $item->route_name ?: ($item->url ?: 'No destination') }} · {{ $item->is_visible ? 'Visible' : 'Hidden' }}</small>
    </div>
    <a href="#edit-{{ $item->id }}">Edit</a>
    @if($item->children->isNotEmpty())
        <div class="children">
            @foreach($item->children as $child)
                @include('admin.navigation._item', ['item' => $child])
            @endforeach
        </div>
    @endif
</div>