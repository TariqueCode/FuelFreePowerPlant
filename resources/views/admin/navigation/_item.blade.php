<div class="menu-row" draggable="true" data-id="{{ $item->id }}" data-kind="{{ $item->source_type }}" aria-grabbed="false">
    <div class="menu-card">
        <div class="menu-card-top">
            <div class="menu-card-title">
                <span class="drag-handle" title="Drag to reorder" aria-label="Drag to reorder" role="button" tabindex="0"><i class="fa-solid fa-grip-vertical"></i></span>
                <span class="type-icon {{ $item->source_type === 'folder' ? 'folder' : 'source' }}"><i class="fa-solid {{ $item->source_type === 'folder' ? 'fa-folder' : ($item->source_type === 'external_link' ? 'fa-link' : 'fa-bolt') }}"></i></span>
                <span class="menu-card-name">{{ $item->displayLabel() }}</span>
            </div>
            <div class="item-actions">
                @if($item->parent_id !== null)
                    <form method="POST" action="{{ route('admin.menu-builder.update', $item) }}" class="promote-form" title="Move to top level">
                        @csrf @method('PATCH')
                        <input type="hidden" name="label" value="{{ $item->displayLabel() }}">
                        <input type="hidden" name="parent_id" value="">
                        <input type="hidden" name="target" value="{{ $item->target }}">
                        <input type="hidden" name="icon" value="{{ $item->icon }}">
                        <input type="hidden" name="is_visible" value="{{ $item->is_visible ? 1 : 0 }}">
                        <button type="submit" class="move-btn promote-btn" aria-label="Move {{ $item->displayLabel() }} to top level"><i class="fa-solid fa-arrow-turn-up"></i></button>
                    </form>
                @endif
                <button type="button" class="move-btn move-up" data-id="{{ $item->id }}" title="Move up" aria-label="Move {{ $item->displayLabel() }} up"><i class="fa-solid fa-arrow-up"></i></button>
                <button type="button" class="move-btn move-down" data-id="{{ $item->id }}" title="Move down" aria-label="Move {{ $item->displayLabel() }} down"><i class="fa-solid fa-arrow-down"></i></button>
                <button type="button" class="rename-btn" onclick="document.getElementById('rename-{{ $item->id }}').hidden=false; document.getElementById('rename-{{ $item->id }}').querySelector('input[name=label]').focus();" title="Rename navigation item"><i class="fa-solid fa-pen"></i></button>
            </div>
        </div>
        <div class="menu-card-meta">
            <span class="type-pill">{{ $item->source_type === 'folder' ? 'Folder' : ($item->source_type === 'external_link' ? 'Custom link' : 'Live source') }}</span>
            <span>{{ $item->is_visible ? 'Visible' : 'Hidden' }}</span>
            @if($item->permission_key)<span>{{ $item->permission_key }}</span>@endif
            @if($item->parent_id !== null)<span class="parent-pill">Sub-menu</span>@endif
        </div>
        <small class="menu-card-url">{{ $item->route_name ?: ($item->url ?: 'Structural folder') }}</small>
        @if($item->source_type === 'folder')
            <div class="nest-hint"><i class="fa-solid fa-folder-tree"></i> Drop items here with <kbd>Alt</kbd> to nest</div>
        @endif
        <div class="inline-label-edit" id="rename-{{ $item->id }}" hidden>
            <form method="POST" action="{{ route('admin.menu-builder.update', $item) }}">
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
    @if($item->children->isNotEmpty())
        <div class="menu-children" data-parent-id="{{ $item->id }}">
            @foreach($item->children as $child)
                @include('admin.navigation._item', ['item' => $child])
            @endforeach
        </div>
    @else
        @if($item->source_type === 'folder')<div class="menu-children empty-drop-zone" data-parent-id="{{ $item->id }}"><span>Drop here to place inside {{ $item->displayLabel() }}</span></div>@endif
    @endif
</div>
