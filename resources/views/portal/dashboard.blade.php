@extends('layouts.portal')

@section('title', 'Client Portal')

@section('content')
<section class="hero">
    <div class="eyebrow">CLIENT PORTAL</div>
    <h1>Welcome, {{ auth()->user()->name }}.</h1>
    <p>Your secure workspace for documents, managed services, communication and infrastructure requests.</p>
</section>

<section class="grid" aria-label="Client services">
    <article class="card"><span class="card-label">Documents</span><strong class="card-value">—</strong><span class="card-note">Secure files</span></article>
    <article class="card"><span class="card-label">Email</span><strong class="card-value">—</strong><span class="card-note">Your mailboxes</span></article>
    <article class="card"><span class="card-label">Subdomains</span><strong class="card-value">—</strong><span class="card-note">Managed services</span></article>
    <article class="card"><span class="card-label">Support</span><strong class="card-value">—</strong><span class="card-note">Requests & updates</span></article>
</section>

<section class="section card">
    <h2>Your secure workspace</h2>
    <p>The portal foundation is ready. Your account will only see modules and actions permitted by its assigned role and permissions.</p>
</section>
@endsection
