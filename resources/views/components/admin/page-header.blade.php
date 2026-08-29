<header class="admin-page-header">
    <div class="admin-page-header__copy">
        @if(isset($eyebrow) && trim($eyebrow) !== '')<span class="admin-eyebrow">{{ $eyebrow }}</span>@endif
        <h1 class="admin-title">{{ $title }}</h1>
        @if(isset($description) && trim($description) !== '')<p class="admin-description">{{ $description }}</p>@endif
    </div>
    @if(isset($actions) && trim($actions) !== '')<div class="admin-actions">{{ $actions }}</div>@endif
</header>