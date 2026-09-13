@once
<link rel="stylesheet" href="{{ asset('admin-premium.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-dashboard.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-builder-polish.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-brand-polish.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('admin-footer-polish.css') }}?v=1">
<script src="{{ asset('admin-documents.js') }}?v=4" defer></script>
@endonce
<style>
@media (min-width: 901px){.sidebar .nav>a,.sidebar .nav-parent{font-size:15px!important}.sidebar .nav-sub a{font-size:14px!important}.sidebar .nav-label{font-size:10px!important}.content{font-size:15px!important}.content p,.content label,.content li,.content td,.content th,.content input,.content select,.content textarea,.content button,.content a,.content span,.content strong{font-size:15px!important}.content h2{font-size:22px!important}.content h3{font-size:19px!important}.content small{font-size:13px!important}.content .profile-info strong{font-size:15px!important}.content .profile-info span{font-size:13px!important}.content .folder-title h2{font-size:22px!important}.content .folder-title span,.content .folder-meta,.content .profile-status,.content .folder-status{font-size:13px!important}.content .hero p,.content .builder-note,.content .notice,.content .errors{font-size:14px!important}.content .primary,.content .secondary,.content .add-profile{font-size:14px!important}}
</style>
@php
$hasChildren=$item->children->isNotEmpty();
$icon=trim((string)$item->icon);
if($icon===''){$icon=$hasChildren?'fa-folder-tree':'fa-circle-dot';}
elseif(!str_contains($icon,' ')){$icon='fa-solid '.(str_starts_with($icon,'fa-')?$icon:'fa-'.$icon);}
@endphp
@if($hasChildren)<div class="nav-group {{ $item->children->contains(fn($child)=>request()->url()===$child->url)?'open':'' }}"><button type="button" class="nav-parent" aria-expanded="{{ $item->children->contains(fn($child)=>request()->url()===$child->url)?'true':'false' }}"><span class="nav-icon"><i class="{{ $icon }}"></i></span><span>{{ $item->displayLabel() }}</span><i class="fa-solid fa-chevron-down nav-chevron"></i></button><div class="nav-sub">@foreach($item->children as $child)@include('layouts._dashboard-navigation-item',['item'=>$child])@endforeach</div></div>
@else<a class="{{ request()->url()===$item->url?'active':'' }}" href="{{ $item->url }}" @if($item->target==='_blank') target="_blank" rel="noopener noreferrer" @endif><span class="nav-icon"><i class="{{ $icon }}"></i></span><span>{{ $item->displayLabel() }}</span></a>@endif