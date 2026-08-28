@extends('layouts.public')

@php
    $siteName = $brand['name'] ?? config('fuelfree.company.name');
    $sectionTitle = $titles[$section] ?? ucfirst(str_replace('-', ' ', $section));
    $intro = $section === 'gallery'
        ? 'Events, activities, milestones and selected moments from Fuel Free Power Plant.'
        : ($page?->excerpt ?: ($brand['tagline'] ?? config('fuelfree.company.tagline')));
@endphp

@section('title', $sectionTitle.' — '.$siteName)

@section('content')
<style>
.site-section-shell{width:min(1180px,calc(100% - 28px));margin:auto}
.site-section-main{padding-bottom:20px}
.site-section-hero{padding:78px 0 38px}
.site-section-hero h1{font-size:clamp(42px,6vw,70px);line-height:.96;margin:16px 0}
.site-section-hero p{max-width:760px;color:#8daab5;line-height:1.85;font-size:16px}
.site-section-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.site-section-card,.site-section-rich,.site-section-empty{border:1px solid rgba(86,210,238,.15);border-radius:18px;background:linear-gradient(145deg,rgba(8,38,52,.82),rgba(3,21,30,.8));overflow:hidden}
.site-section-card img{width:100%;height:185px;object-fit:cover;display:block}
.site-section-body,.site-section-rich{padding:20px}
.site-section-body h2,.site-section-rich h2{margin:0 0 8px;font-size:20px}
.site-section-body p,.site-section-rich{color:#8daab5;font-size:16px;line-height:1.8}
.site-section-rich h1,.site-section-rich h2,.site-section-rich h3,.site-section-body h1,.site-section-body h2,.site-section-body h3{color:#effcff;line-height:1.25}
.site-section-rich table,.site-section-body table{width:100%;border-collapse:collapse;margin:18px 0}
.site-section-rich th,.site-section-rich td,.site-section-body th,.site-section-body td{border:1px solid rgba(86,210,238,.15);padding:9px;text-align:left}
.site-section-rich th,.site-section-body th{background:rgba(67,209,240,.08);color:#effcff}
.site-section-rich blockquote,.site-section-body blockquote{margin:16px 0;padding:12px 16px;border-left:4px solid #43d1f0;background:rgba(67,209,240,.06)}
.site-section-rich pre,.site-section-body pre{padding:13px;border-radius:10px;background:#031017;overflow:auto;color:#c8f3fa}
.site-section-rich hr,.site-section-body hr{border:0;border-top:1px solid rgba(86,210,238,.15);margin:22px 0}
.site-section-rich .editor-callout,.site-section-body .editor-callout{padding:14px 16px;border:1px solid rgba(67,209,240,.22);border-radius:12px;background:rgba(67,209,240,.06);margin:16px 0}
.site-section-rich .editor-button,.site-section-body .editor-button{display:inline-flex;align-items:center;gap:7px;padding:9px 14px;border-radius:9px;background:#29aaca;color:#fff!important;text-decoration:none!important;font-weight:700}
.site-section-rich iframe,.site-section-body iframe{width:100%;min-height:320px;border:0;border-radius:12px;margin:12px 0}
.site-section-empty{padding:20px;color:#8daab5}
@media(max-width:900px){.site-section-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:720px){.site-section-hero{padding:55px 0 28px}.site-section-hero h1{font-size:44px}}
@media(max-width:520px){.site-section-shell{width:calc(100% - 24px)}.site-section-grid{grid-template-columns:1fr 1fr}.site-section-rich table,.site-section-body table{display:block;overflow-x:auto;white-space:nowrap}.site-section-rich iframe,.site-section-body iframe{min-height:210px}}
</style>

<main class="site-section-shell site-section-main">
    <section class="site-section-hero">
        <h1>{{ $sectionTitle }}</h1>
        <p>{{ $intro }}</p>
    </section>

    @if($page?->content)
        <section class="site-section-rich">{!! $page->content !!}</section>
    @endif

    @if($section === 'about-us')
        @if($companyItems->isEmpty())
            <div class="site-section-empty">No company information has been published yet.</div>
        @else
            @foreach($companyItems as $item)
                <article class="site-section-rich" style="margin-bottom:14px">
                    <h2>{{ $item->title }}</h2>
                    @if($item->excerpt)<p>{{ $item->excerpt }}</p>@endif
                    {!! $item->content !!}
                </article>
            @endforeach
        @endif
    @elseif($section === 'solutions' || $section === 'plants' || $section === 'future-project')
        <section class="site-section-grid">
            @if($items->isEmpty())
                <div class="site-section-empty">No {{ $section === 'plants' ? 'plant information' : ($section === 'future-project' ? 'future project information' : 'solutions') }} have been published yet.</div>
            @else
                @foreach($items as $item)
                    <article class="site-section-card">
                        @if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title }}">@endif
                        <div class="site-section-body">
                            <h2>{{ $item->title }}</h2>
                            @if($item->excerpt)<p>{{ $item->excerpt }}</p>@endif
                            {!! $item->content !!}
                        </div>
                    </article>
                @endforeach
            @endif
        </section>
    @elseif($section === 'career')
        <section class="site-section-grid">
            @if($items->isEmpty())
                <div class="site-section-empty">No career information has been published yet.</div>
            @else
                @foreach($items as $item)
                    <article class="site-section-card">
                        @if($item->image_path)<img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title }}">@endif
                        <div class="site-section-body">
                            <h2>{{ $item->title }}</h2>
                            <p>{{ $item->excerpt }}</p>
                            {!! $item->content !!}
                        </div>
                    </article>
                @endforeach
            @endif
        </section>
    @endif
</main>
@endsection
