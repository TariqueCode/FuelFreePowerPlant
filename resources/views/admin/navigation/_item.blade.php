@once
<style>
@media(max-width:700px){
    .content .menu-card-top{display:grid!important;grid-template-columns:22px 30px minmax(0,1fr) auto 34px!important;align-items:center!important;gap:7px!important}
    .content .menu-card-title{display:contents!important}
    .content .drag-handle{grid-column:1!important;grid-row:1!important;width:22px!important;height:28px!important;padding:3px!important;display:grid!important;place-items:center!important;flex:0 0 22px!important}
    .content .type-icon{grid-column:2!important;grid-row:1!important;width:28px!important;height:28px!important}
    .content .menu-card-name{grid-column:3!important;grid-row:1!important;display:block!important;min-width:0!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important;font-size:16px!important;line-height:1.25!important}
    .content .menu-card-title .type-pill{grid-column:4!important;grid-row:1!important;white-space:nowrap!important;font-size:7px!important;padding:3px 5px!important}
    .content .item-actions{grid-column:5!important;grid-row:1!important;display:flex!important;align-items:center!important;justify-content:center!important;gap:0!important;min-width:34px!important;flex:0 0 34px!important}
    .content .rename-btn{width:28px!important;height:28px!important;display:grid!important;place-items:center!important;margin:0!important}
    .content .move-btn{display:none!important}
}
@media(max-width:420px){
    .content .menu-card-top{grid-template-columns:20px 27px minmax(0,1fr) auto 30px!important;gap:5px!important}
    .content .drag-handle{width:20px!important;height:26px!important;flex-basis:20px!important}
    .content .type-icon{width:26px!important;height:26px!important}
    .content .menu-card-name{font-size:15px!important}
    .content .menu-card-title .type-pill{font-size:6.5px!important;padding:3px 4px!important}
    .content .item-actions{min-width:30px!important;flex-basis:30px!important}
    .content .rename-btn{width:26px!important;height:26px!important}
}
</style>
@endonce
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
            <form method="POST" action="{{ route('admin.navigation.update',$item) }}">
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
