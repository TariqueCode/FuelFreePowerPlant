@php
    $isLegacyBoardFolder = $item->source_type === 'folder' && str_starts_with((string) $item->source_key, 'management_folder:');
    $isFolder = $item->source_type === 'folder' && ! $isLegacyBoardFolder;
@endphp
<div class="menu-row" draggable="true" data-id="{{ $item->id }}" data-kind="{{ $isFolder ? 'folder' : ($item->source_type === 'external_link' ? 'external_link' : 'route') }}" aria-grabbed="false">
    <div class="menu-card">
        <div class="menu-card-top">
            <div class="menu-card-title">
                <span class="drag-handle" title="Drag to reorder" aria-label="Drag to reorder" role="button" tabindex="0"><i class="fa-solid fa-grip-vertical"></i></span>
                <span class="type-icon {{ $isFolder ? 'folder' : 'source' }}"><i class="fa-solid {{ $isFolder ? 'fa-folder' : ($item->source_type === 'external_link' ? 'fa-link' : 'fa-bolt') }}"></i></span>
                <span class="menu-card-name">{{ $item->displayLabel() }}</span>
                <span class="type-pill"><i class="fa-solid {{ $item->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i> {{ $item->is_visible ? 'Visible' : 'Hidden' }}</span>
            </div>
            <div class="item-actions">
                <button type="button" class="rename-btn" onclick="document.getElementById('rename-{{ $item->id }}').hidden=false; document.getElementById('rename-{{ $item->id }}').querySelector('input[name=label]').focus();" title="Rename navigation item" aria-label="Rename {{ $item->displayLabel() }}"><i class="fa-solid fa-pen"></i></button>
            </div>
        </div>
        <div class="inline-label-edit" id="rename-{{ $item->id }}" hidden>
            <form method="POST" action="{{ route('admin.menu-builder.update',$item) }}">
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
    @endif
</div>
