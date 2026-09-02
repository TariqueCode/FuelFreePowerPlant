@extends('layouts.public')

@section('title', ($resource->meta_title ?: $resource->title).' · '.($brand['name'] ?? config('fuelfree.company.name')))

@section('content')
@push('head')
@if($resource->meta_description)<meta name="description" content="{{ $resource->meta_description }}">@endif
<meta property="og:title" content="{{ $resource->meta_title ?: $resource->title }}">
@if($resource->meta_description)<meta property="og:description" content="{{ $resource->meta_description }}">@endif
<meta property="og:type" content="article">
@endpush

<style>body{margin:0;background:#020b12;color:#eefaff;font-family:Inter,system-ui,sans-serif}.wrap{width:min(900px,calc(100% - 28px));margin:auto}.back{display:inline-flex;gap:8px;color:#59d8f2;text-decoration:none;font-size:12px;margin-top:35px}.hero{padding:55px 0 25px}.ey{color:#45d1f0;text-transform:uppercase;letter-spacing:.16em;font-size:10px}.hero h1{font-size:clamp(34px,6vw,58px);margin:12px 0}.muted{color:#8daab5;line-height:1.8}.download{display:inline-flex;align-items:center;gap:8px;margin-top:14px;padding:10px 14px;border-radius:10px;background:rgba(69,209,240,.1);border:1px solid rgba(69,209,240,.2);color:#5ed8f2;text-decoration:none;font-size:11px;font-weight:800}.article{border:1px solid rgba(70,210,240,.16);background:linear-gradient(145deg,#092736,#03161f);border-radius:20px;padding:clamp(20px,5vw,42px);line-height:1.9;color:#c9dce2}.article img{max-width:100%;border-radius:14px}.page-builder-content{display:grid;gap:18px;margin:0 0 22px}.pb-hero,.pb-image-text{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(260px,.85fr);gap:20px;align-items:center;padding:22px;border:1px solid rgba(70,210,240,.13);border-radius:18px;background:linear-gradient(145deg,#092736,#03161f)}.pb-hero h2,.pb-image-text h2,.pb-text h2{margin:0 0 9px;color:#eefaff}.pb-hero img,.pb-image-text img,.pb-image img{width:100%;max-width:100%;height:auto;border-radius:13px;object-fit:cover}.pb-image{margin:0;padding:16px;border:1px solid rgba(70,210,240,.13);border-radius:16px;background:rgba(8,38,52,.6)}.pb-image figcaption{margin-top:8px;color:#9bbbc4;font-size:12px}.pb-video iframe{width:100%;min-height:390px;border:0;border-radius:14px}.content-cta{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 15px;border-radius:9px;background:#29aaca;color:#fff!important;text-decoration:none!important;font-weight:800}.media-gallery{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px}.media-gallery img{width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:10px}.page-builder-content hr{border:0;border-top:1px solid rgba(70,210,240,.15);margin:6px 0}.pb-text{padding:2px 0}.pb-text h2{font-size:clamp(22px,3vw,32px)}@media(max-width:700px){.pb-hero,.pb-image-text{grid-template-columns:1fr;padding:16px}.pb-video iframe{min-height:220px}.media-gallery{grid-template-columns:1fr 1fr}}.related{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin:35px 0 70px}.rel{padding:18px;border:1px solid rgba(70,210,240,.13);border-radius:15px;color:#dff8ff;text-decoration:none}.rel small{color:#58d5ee}@media(max-width:600px){.related{grid-template-columns:1fr}}</style>
<main class="wrap"><a class="back" href="{{ route('resources.index') }}"><i class="fa-solid fa-arrow-left"></i> All resources</a><section class="hero"><span class="ey">Resource</span><h1>{{ $resource->title }}</h1>@if($resource->published_at)<p class="muted">Published {{ $resource->published_at->format('F j, Y') }}</p>@endif @if($resource->attachment_path)<a class="download" href="{{ route('resources.download',$resource->slug) }}"><i class="fa-solid fa-file-arrow-down"></i> Download PDF{{ $resource->attachment_size ? ' · '.number_format($resource->attachment_size / 1048576, 1).' MB' : '' }}</a>@endif</section>@if(!empty($resource->builder_blocks))
<section class="page-builder-content" aria-label="Resource sections">
@foreach($resource->builder_blocks as $block)
@if(($block['visible'] ?? true) === false) @continue @endif
@php($blockType=$block['type'] ?? 'text')
@if($blockType==='hero')
<section class="pb-hero"><div>@if(!empty($block['title']))<h2>{{ $block['title'] }}</h2>@endif<div>{!! $block['content'] ?? '' !!}</div></div>@if(!empty($block['image']))<img src="{{ $block['image'] }}" alt="{{ $block['title'] ?? $resource->title }}" loading="lazy">@endif</section>
@elseif($blockType==='image')
@if(!empty($block['image']))<figure class="pb-image"><img src="{{ $block['image'] }}" alt="{{ $block['title'] ?? $resource->title }}" loading="lazy">@if(!empty($block['title']))<figcaption>{{ $block['title'] }}</figcaption>@endif</figure>@endif
@elseif($blockType==='image_text')
<section class="pb-image-text">@if(!empty($block['image']))<img src="{{ $block['image'] }}" alt="{{ $block['title'] ?? $resource->title }}" loading="lazy">@endif<div>@if(!empty($block['title']))<h2>{{ $block['title'] }}</h2>@endif<div>{!! $block['content'] ?? '' !!}</div></div></section>
@elseif($blockType==='video')
@if(!empty($block['url']))<div class="pb-video"><iframe src="{{ $block['url'] }}" title="{{ $block['title'] ?? 'Video' }}" loading="lazy" allowfullscreen></iframe></div>@endif
@elseif($blockType==='cta' || $blockType==='button')
@if(!empty($block['url']))<a class="content-cta" href="{{ $block['url'] }}">{{ $block['title'] ?: 'Learn more' }}</a>@endif
@elseif($blockType==='divider')
<hr>
@elseif($blockType==='html')
{!! $block['content'] ?? '' !!}
@elseif($blockType==='gallery')
@if(!empty($block['image']))<div class="media-gallery"><img src="{{ $block['image'] }}" alt="{{ $block['title'] ?? $resource->title }}" loading="lazy"></div>@endif
@else
<section class="pb-text">@if(!empty($block['title']))<h2>{{ $block['title'] }}</h2>@endif{!! $block['content'] ?? '' !!}</section>
@endif
@endforeach
</section>
@endif
<article class="article">@if($resource->image_path)<img src="{{ asset('storage/'.$resource->image_path) }}" alt="{{ $resource->cover_alt ?: $resource->title }}" loading="lazy">@endif<div>{!! $resource->content !!}</div></article>@if($related->count())<section><h2>Related resources</h2><div class="related">@foreach($related as $item)<a class="rel" href="{{ route('resources.show',$item->slug) }}"><small><i class="fa-solid fa-file-lines"></i> RESOURCE</small><div>{{ $item->title }}</div></a>@endforeach</div></section>@endif</main>
@endsection
