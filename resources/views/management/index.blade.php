@extends('layouts.app')
@section('title','Management')
@section('content')
<section class="management-page">
    <div class="management-hero"><span class="eyebrow">LEADERSHIP & MANAGEMENT</span><h1>Management Team</h1><p>Meet the people responsible for guiding FuelFree PowerPlant.</p></div>
    <div class="management-grid">
        @forelse($members as $member)
            <article class="member-card">
                <div class="member-photo">
                    @if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="{{ $member->title }}" loading="lazy">@else<div class="member-placeholder"><i class="fa-solid fa-user-tie"></i></div>@endif
                </div>
                <div class="member-body"><h2>{{ $member->title }}</h2>@if($member->excerpt)<p class="member-role">{{ $member->excerpt }}</p>@endif@if($member->content)<div class="member-bio">{!! $member->content !!}</div>@endif</div>
            </article>
        @empty
            <div class="management-empty"><i class="fa-solid fa-people-group"></i><h2>Management information will be published soon.</h2></div>
        @endforelse
    </div>
</section>
@endsection
@push('styles')
<style>
.management-page{max-width:1180px;margin:0 auto;padding:clamp(28px,6vw,70px) 18px}.management-hero{max-width:760px;margin-bottom:34px}.eyebrow{font-size:10px;letter-spacing:.18em;color:#56c9e7}.management-hero h1{margin:10px 0 8px;font-size:clamp(32px,5vw,56px);line-height:1.05}.management-hero p{margin:0;color:#7898a5;line-height:1.7}.management-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.member-card{overflow:hidden;border:1px solid rgba(104,204,235,.13);border-radius:22px;background:rgba(255,255,255,.025);box-shadow:0 18px 50px rgba(0,0,0,.16)}.member-photo{aspect-ratio:4/3;background:linear-gradient(145deg,#071923,#0c2733);overflow:hidden}.member-photo img{width:100%;height:100%;object-fit:cover;display:block}.member-placeholder{height:100%;display:grid;place-items:center;color:#5aa8ba;font-size:42px}.member-body{padding:20px}.member-body h2{margin:0;font-size:19px;color:#e7f7fa}.member-role{margin:7px 0 0;color:#5dc7df;font-size:11px;font-weight:600}.member-bio{margin-top:14px;color:#89a8b2;font-size:12px;line-height:1.7}.member-bio p{margin:0 0 8px}.management-empty{grid-column:1/-1;min-height:260px;border:1px dashed rgba(104,204,235,.18);border-radius:20px;display:grid;place-items:center;text-align:center;padding:30px;color:#7696a2}.management-empty i{font-size:34px;color:#4aa9bd}.management-empty h2{font-size:16px;font-weight:500}@media(max-width:850px){.management-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.management-page{padding:25px 14px}.management-grid{grid-template-columns:1fr}.member-card{border-radius:18px}}
</style>
@endpush
