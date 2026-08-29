@php($variant = $variant ?? 'secondary')
<a {{ $attributes->merge(['class'=>'admin-btn admin-btn--'.$variant]) }}>{{ $slot }}</a>