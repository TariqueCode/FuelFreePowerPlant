@extends('layouts.public')

@php
    $siteName = $brand['name'] ?? config('fuelfree.company.name');
@endphp

@section('title', $item->title.' — '.$siteName)

@section('content')
<style>
.company-page-shell{width:min(1100px,calc(100% - 28px));margin:auto;padding:45px 0 55px}
.company-page-back{display:inline-flex;gap:8px;align-items:center;color:#8eb7c2;text-decoration:none;margin-bottom:20px;font-size:14px}
.company-page-hero{border:1px solid rgba(86,210,238,.15);border-radius:22px;background:linear-gradient(145deg,rgba(8,38,52,.95),rgba(3,21,30,.92));margin-bottom:25px}
.company-page-hero-content{padding:clamp(26px,5vw,48px);max-width:820px}
.company-page-hero h1{font-size:clamp(34px,6vw,60px);line-height:1.08;margin:0 0 14px;text-shadow:0 3px 18px rgba(0,0,0,.35)}
.company-page-hero p{color:#c2d8de;font-size:18px;line-height:1.7;margin:0;max-width:760px}
.company-page-content{border:1px solid rgba(86,210,238,.15);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.82),rgba(3,21,30,.8));padding:clamp(20px,4vw,36px);font-size:17px;line-height:1.85;color:#c7dce2}
.company-page-content img,.company-page-content video{max-width:100%;height:auto;border-radius:14px}
.company-page-content h1,.company-page-content h2,.company-page-content h3{color:#effcff}
.company-page-content h1{font-size:clamp(28px,4vw,44px);line-height:1.2}
.company-page-content h2,.company-page-content h3{margin-top:1.4em}
.company-page-content table{width:100%;border-collapse:collapse;margin:22px 0}
.company-page-content th,.company-page-content td{border:1px solid rgba(86,210,238,.15);padding:10px;text-align:left}
.company-page-content th{background:rgba(67,209,240,.08);color:#effcff}
.company-page-content blockquote{margin:20px 0;padding:14px 18px;border-left:4px solid #43d1f0;background:rgba(67,209,240,.06);border-radius:0 10px 10px 0}
.company-page-content pre{padding:16px;border-radius:12px;background:#031017;overflow:auto;color:#c8f3fa}
.company-page-content hr{border:0;border-top:1px solid rgba(86,210,238,.15);margin:28px 0}
.company-page-content .editor-callout{padding:16px 18px;border:1px solid rgba(67,209,240,.22);border-radius:12px;background:rgba(67,209,240,.06);margin:18px 0}
.company-page-content .editor-button{display:inline-flex;align-items:center;gap:7px;padding:10px 16px;border-radius:9px;background:#29aaca;color:#fff!important;text-decoration:none!important;font-weight:700;margin:6px 4px}
.company-page-content iframe{width:100%;min-height:360px;border:0;border-radius:14px;margin:16px 0}
@media(max-width:600px){.company-page-shell{padding-top:30px}.company-page-hero{border-radius:17px}.company-page-hero-content{padding:24px 20px}.company-page-hero h1{font-size:36px}.company-page-hero p,.company-page-content{font-size:16px;line-height:1.8}.company-page-content table{display:block;overflow-x:auto;white-space:nowrap}.company-page-content iframe{min-height:220px}}
</style>

<main class="company-page-shell">
    <a class="company-page-back" href="{{ route('site.about') }}"><i class="fa-solid fa-arrow-left"></i> Back to Company</a>
    <section class="company-page-hero">
        <div class="company-page-hero-content">
            <h1>{{ $item->title }}</h1>
            @if($item->excerpt)<p>{{ $item->excerpt }}</p>@endif
        </div>
    </section>
    <article class="company-page-content">{!! $item->content !!}</article>
</main>
@endsection
