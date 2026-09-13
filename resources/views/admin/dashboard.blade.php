@extends('layouts.portal')
@section('title','Admin Dashboard')
@section('content')
<section class="dashboard-welcome" aria-labelledby="dashboard-welcome-title">
    <div class="eyebrow">ENERGY OPERATIONS CONTROL CENTER</div>
    <h1 id="dashboard-welcome-title">Powering the future, <span>{{ auth()->user()->name }}</span>.</h1>
    <p>A focused control center for managing the FuelFree PowerPlant website and administration.</p>
</section>
@push('styles')
<style>
.dashboard-welcome{padding:18px 0 30px;max-width:980px}
.dashboard-welcome .eyebrow{color:#61c9e8;font-size:11px;font-weight:800;letter-spacing:.14em}
.dashboard-welcome h1{margin:10px 0 12px;font-size:clamp(32px,4vw,52px);line-height:1.06;letter-spacing:-.03em}
.dashboard-welcome h1 span{background:linear-gradient(100deg,#eefbff,#63d8f3);-webkit-background-clip:text;background-clip:text;color:transparent}
.dashboard-welcome p{margin:0;max-width:760px;color:#86a5b4;line-height:1.7;font-size:15px}
@media(max-width:700px){.dashboard-welcome{padding:6px 0 24px}.dashboard-welcome .eyebrow{font-size:10px}.dashboard-welcome h1{font-size:30px;line-height:1.1}.dashboard-welcome p{font-size:14px;line-height:1.65}}
</style>
@endpush
@endsection