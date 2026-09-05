<style>
@media (min-width: 901px) {
    /* Accessibility/readability: keep mobile sizing unchanged while giving
       desktop administrators a larger, easier-to-read interface. */
    .sidebar .nav > a,
    .sidebar .nav-parent {
        font-size: 14px;
    }

    .sidebar .nav-sub a {
        font-size: 12px;
    }

    .sidebar .nav-label {
        font-size: 10px;
    }

    .content {
        font-size: 14px;
    }

    .content p,
    .content label,
    .content li,
    .content td,
    .content th,
    .content input,
    .content select,
    .content textarea,
    .content button,
    .content a {
        font-size: 14px;
    }

    .content h2 {
        font-size: 21px;
    }

    .content h3 {
        font-size: 18px;
    }

    .content small {
        font-size: 12px;
    }
}
</style>

@php($hasChildren = $item->children->isNotEmpty())
@if($hasChildren)
<div class="nav-group {{ $item->children->contains(fn($child) => request()->url() === $child->url) ? 'open' : '' }}">
    <button type="button" class="nav-parent" aria-expanded="{{ $item->children->contains(fn($child) => request()->url() === $child->url) ? 'true' : 'false' }}">
        <span class="nav-icon"><i class="fa-solid fa-folder-tree"></i></span><span>{{ $item->displayLabel() }}</span><i class="fa-solid fa-chevron-down nav-chevron"></i>
    </button>
    <div class="nav-sub">
        @foreach($item->children as $child)
            @include('layouts._dashboard-navigation-item',['item'=>$child])
        @endforeach
    </div>
</div>
@else
<a class="{{ request()->url() === $item->url ? 'active' : '' }}" href="{{ $item->url }}" @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
    <span class="nav-icon"><i class="fa-solid fa-circle-dot"></i></span><span>{{ $item->displayLabel() }}</span>
</a>
@endif