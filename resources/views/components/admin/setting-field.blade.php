<div class="admin-field">
    @if(isset($label))<label class="admin-field__label" for="{{ $id ?? $attributes->get('id') }}">{{ $label }}</label>@endif
    {{ $slot }}
    @if(isset($help) && trim($help) !== '')<small class="admin-field__help">{{ $help }}</small>@endif
</div>