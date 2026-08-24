@extends('layouts.portal')

@section('title', 'Admin Dashboard')

@section('content')
<section class="hero">
    <div class="eyebrow">OPERATIONS CONTROL CENTER</div>
    <h1>Good to see you, {{ auth()->user()->name }}.</h1>
    <p>Your operational workspace is live. The metrics below are pulled from the current account and private document vault.</p>
</section>

<section class="grid" aria-label="Platform overview">
    @if(!is_null($users))
        <article class="card"><span class="card-label">Users</span><strong class="card-value">{{ number_format($users) }}</strong><span class="card-note">Registered platform accounts</span></article>
    @endif
    <article class="card"><span class="card-label">Files</span><strong class="card-value">{{ number_format($documents) }}</strong><span class="card-note">Files in your secure vault</span></article>
    <article class="card"><span class="card-label">Folders</span><strong class="card-value">{{ number_format($folders) }}</strong><span class="card-note">Private folders</span></article>
    <article class="card"><span class="card-label">Storage</span><strong class="card-value">{{ number_format($storageBytes / 1073741824, 2) }} GB</strong><span class="card-note">Current private storage usage</span></article>
</section>

<section class="section card">
    <h2>Platform modules</h2>
    <p>Authentication, role permissions, the responsive dashboard and secure document vault are active. Email and Support remain permission-aware service modules and are ready for their infrastructure/data integrations.</p>
</section>
@endsection
