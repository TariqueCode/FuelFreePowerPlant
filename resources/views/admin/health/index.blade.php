@extends('layouts.portal')

@section('title', 'System Health')
@section('content')
<section class="hero"><div class="eyebrow">PRODUCTION HARDENING</div><h1>System Health</h1><p>Live checks for the application, database, private storage and production safety configuration.</p></section>
<div class="health-summary"><strong>{{ collect($checks)->every(fn($check) => $check['status']) ? 'All checks passed' : 'Attention required' }}</strong><span>{{ collect($checks)->where('status', true)->count() }} of {{ count($checks) }} checks healthy</span></div>
<div class="health-grid">
@foreach($checks as $name => $check)
<article class="health-card {{ $check['status'] ? 'ok' : 'bad' }}"><div class="icon">{{ $check['status'] ? '✓' : '!' }}</div><div><strong>{{ $name }}</strong><span>{{ $check['detail'] }}</span></div></article>
@endforeach
</div>
@endsection

@push('styles')
<style>.health-summary{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:14px;padding:14px 16px;border:1px solid var(--line);border-radius:14px;background:rgba(255,255,255,.025)}.health-summary strong{font-size:13px}.health-summary span{font-size:11px;color:var(--muted)}.health-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.health-card{display:flex;align-items:center;gap:14px;padding:18px;border:1px solid var(--line);border-radius:16px;background:rgba(255,255,255,.025)}.health-card.ok .icon{background:rgba(67,194,137,.14);color:#7be0b3}.health-card.bad .icon{background:rgba(230,76,88,.14);color:#ff9ba4}.icon{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-weight:900}.health-card strong,.health-card span{display:block}.health-card strong{font-size:13px}.health-card span{margin-top:4px;color:var(--muted);font-size:11px}@media(max-width:650px){.health-grid{grid-template-columns:1fr}}</style>
@endpush
