@extends('layouts.portal')

@section('title', 'Support')
@section('content')
<section class="hero">
    <div class="eyebrow">SERVICE DESK</div>
    <h1>Support</h1>
    <p>Handle client requests, operational issues and service communication from the FuelFree PowerPlant support center.</p>
</section>
<div class="grid">
    <article class="card"><span class="card-label">Open tickets</span><strong class="card-value">—</strong><span class="card-note">Active requests.</span></article>
    <article class="card"><span class="card-label">Priority</span><strong class="card-value">—</strong><span class="card-note">Issues requiring attention.</span></article>
    <article class="card"><span class="card-label">Response</span><strong class="card-value">—</strong><span class="card-note">Service performance.</span></article>
</div>
<section class="section card"><h2>Support center</h2><p>Ticketing, replies, attachments and notifications will connect here. Role and permission checks are already active.</p></section>
@endsection
