<style>
@media (min-width: 901px) {
    /* Accessibility/readability: keep mobile sizing unchanged while giving
       desktop administrators a larger, easier-to-read interface. */
    .sidebar .nav > a,
    .sidebar .nav-parent {
        font-size: 15px !important;
    }

    .sidebar .nav-sub a {
        font-size: 14px !important;
    }

    .sidebar .nav-label {
        font-size: 10px !important;
    }

    .content {
        font-size: 15px !important;
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
    .content a,
    .content span,
    .content strong {
        font-size: 15px !important;
    }

    .content h2 {
        font-size: 22px !important;
    }

    .content h3 {
        font-size: 19px !important;
    }

    .content small {
        font-size: 13px !important;
    }

    /* Profile Builder rows were using very small component-level sizes;
       explicitly lift those labels without changing the mobile layout. */
    .content .profile-info strong {
        font-size: 15px !important;
    }

    .content .profile-info span {
        font-size: 13px !important;
    }

    .content .folder-title h2 {
        font-size: 22px !important;
    }

    .content .folder-title span,
    .content .folder-meta,
    .content .profile-status,
    .content .folder-status {
        font-size: 13px !important;
    }

    .content .hero p,
    .content .builder-note,
    .content .notice,
    .content .errors {
        font-size: 14px !important;
    }

    .content .primary,
    .content .secondary,
    .content .add-profile {
        font-size: 14px !important;
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