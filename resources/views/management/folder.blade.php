@extends('layouts.public')

@php
    $brand = \App\Models\SystemSetting::query()->whereIn('key',['company.name','company.logo_path','company.tagline'])->pluck('value','key');
    $name = $brand->get('company.name') ?: config('fuelfree.company.name');
@endphp

@section('title', $folder->name.' — '.$name)

@section('content')
<style>
.management-page{--line:rgba(91,214,239,.14);--cyan:#43d1f0;--muted:#8eaab5;width:min(1240px,calc(100% - 48px));margin:auto;padding:68px 0 88px;color:#eaf8fb}.management-hero{margin-bottom:38px}.management-eyebrow{display:inline-flex;align-items:center;gap:9px;font-size:10px;letter-spacing:.22em;color:var(--cyan);font-weight:800;text-transform:uppercase}.management-eyebrow:before{content:"";width:28px;height:1px;background:var(--cyan)}.management-hero h1{font-size:clamp(38px,5vw,64px);line-height:1.05;letter-spacing:-.045em;margin:13px 0 12px;font-weight:800}.management-hero p{margin:0;color:var(--muted);font-size:14px;line-height:1.75;max-width:720px}.management-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:22px;align-items:stretch}.management-card{min-width:0;display:flex;flex-direction:column;overflow:hidden;border:1px solid var(--line);border-radius:22px;background:linear-gradient(155deg,rgba(9,42,57,.94),rgba(3,21,30,.98) 62%,rgba(2,15,22,.99));box-shadow:0 18px 55px rgba(0,0,0,.2)}.management-photo{aspect-ratio:4/4.45;background:#eef2f3;overflow:hidden;display:grid;place-items:center}.management-photo img{width:100%;height:100%;object-fit:cover;display:block}.management-photo i{font-size:44px;color:#4fd5ef}.management-body{flex:1;display:flex;flex-direction:column;padding:19px}.management-body h2{margin:0;color:#edf9fb;font-size:19px;line-height:1.3}.management-role{margin:7px 0 0;color:#73def3;font-size:11px;font-weight:800}.management-bio{margin:14px 0;color:#89a8b2;font-size:11px;line-height:1.75;display:-webkit-box;-webkit-line-clamp:5;-webkit-box-orient:vertical;overflow:hidden}.management-contacts{margin-top:auto;display:grid;gap:7px;padding:13px 0;border-top:1px solid rgba(86,210,238,.09)}.management-contact{display:flex;gap:8px;align-items:center;color:#9db7c0;text-decoration:none;font-size:10px;min-width:0;overflow-wrap:anywhere}.management-contact i{width:25px;height:25px;flex:0 0 25px;display:grid;place-items:center;border-radius:7px;background:rgba(67,209,240,.06);color:#58cee8}.management-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:12px}.management-action{display:flex;align-items:center;justify-content:center;gap:7px;padding:10px 7px;border:1px solid var(--line);border-radius:10px;color:#b9d8df;text-decoration:none;font-size:10px;font-weight:800}.management-action.primary{background:linear-gradient(135deg,#2db1cf,#17758d);color:#fff}.management-empty{border:1px dashed var(--line);border-radius:18px;padding:55px 20px;text-align:center;color:var(--muted);grid-column:1/-1}.management-empty i{font-size:38px;color:var(--cyan);margin-bottom:12px}.management-empty h2{margin:0 0 6px;color:#e5f7fa}.management-empty p{margin:0;font-size:11px}@media(max-width:1050px){.management-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.management-page{width:calc(100% - 24px);padding:40px 0 58px}.management-hero{margin-bottom:27px}.management-hero h1{font-size:clamp(34px,10vw,46px)}.management-hero p{font-size:12px}.management-grid{grid-template-columns:1fr;gap:16px}.management-card{border-radius:20px}.management-photo{aspect-ratio:4/4.7}.management-body{padding:17px}.management-body h2{font-size:18px}}
</style>

<div class="management-page">
    <header class="management-hero">
        <span class="management-eyebrow">LEADERSHIP</span>
        <h1>{{ $folder->name }}</h1>
        <p>Meet the people responsible for guiding {{ $name }}.</p>
    </header>

    <section class="management-grid" aria-label="{{ $folder->name }} profiles">
        @forelse($members as $member)
            <article class="management-card">
                <div class="management-photo">
                    @if($member->image_path)
                        <img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}">
                    @else
                        <i class="fa-solid fa-user-tie"></i>
                    @endif
                </div>
                <div class="management-body">
                    <h2>{{ $member->title }}</h2>
                    <div class="management-role">{{ $member->designation ?: $member->excerpt }}</div>
                    @if($member->content)<div class="management-bio">{{ strip_tags($member->content) }}</div>@endif
                    <div class="management-contacts">
                        @if($member->phone)<a class="management-contact" href="tel:{{ preg_replace('/\s+/', '', $member->phone) }}"><i class="fa-solid fa-phone"></i><span>{{ $member->phone }}</span></a>@endif
                        @if($member->email)<a class="management-contact" href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i><span>{{ $member->email }}</span></a>@endif
                    </div>
                    <div class="management-actions">
                        @if($member->content)<a class="management-action primary" href="#profile-{{ $member->id }}"><i class="fa-regular fa-id-card"></i> View Profile</a>@endif
                        <a class="management-action" href="{{ route('management.vcard',$member) }}"><i class="fa-regular fa-address-card"></i> vCard</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="management-empty"><i class="fa-solid fa-people-group"></i><h2>No profiles published yet</h2><p>This folder is ready for profiles from the Profile Builder.</p></div>
        @endforelse
    </section>
</div>
@endsection
