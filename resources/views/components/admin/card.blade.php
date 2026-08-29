<section {{ $attributes->merge(['class'=>'admin-card']) }}>
    @if(isset($header) && trim($header) !== '')<div class="admin-card__header">{{ $header }}</div>@endif
    <div class="admin-card__body">{{ $slot }}</div>
</section>