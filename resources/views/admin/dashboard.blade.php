@extends('layouts.portal')

@section('title', 'Admin Dashboard')

@section('content')
<section class="hero">
    <div class="eyebrow">OPERATIONS CONTROL CENTER</div>
    <h1>Good to see you, {{ auth()->user()->name }}.</h1>
    <p>Monitor the FuelFree PowerPlant platform from one secure workspace. Operational modules will appear here as they are integrated.</p>
</section>

<section class="grid" aria-label="Platform overview">
    <article class="card"><span class="card-label">Projects</span><strong class="card-value">—</strong><span class="card-note">Project portfolio</span></article>
    <article class="card"><span class="card-label">Documents</span><strong class="card-value">—</strong><span class="card-note">Secure vault</span></article>
    <article class="card"><span class="card-label">Mailboxes</span><strong class="card-value">—</strong><span class="card-note">Managed accounts</span></article>
    <article class="card"><span class="card-label">Support</span><strong class="card-value">—</strong><span class="card-note">Open requests</span></article>
</section>

<section class="section card">
    <h2>Platform foundation</h2>
    <p>Authentication, roles, permissions and the responsive dashboard shell are active. CMS, document vault, email, subdomain and support modules will connect to this control center without changing the core navigation architecture.</p>
</section>
@endsection
