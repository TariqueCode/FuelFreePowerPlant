@extends('layouts.portal')
@section('title', 'Client Portal')
@section('content')
<section class="hero"><div class="eyebrow">CLIENT PORTAL</div><h1>Welcome, {{ auth()->user()->name }}.</h1><p>Your secure workspace for documents, managed services, communication and infrastructure requests.</p></section>
<section class="grid" aria-label="Client services">
<article class="card"><span class="card-label">Files</span><strong class="card-value">{{ number_format($documents) }}</strong><span class="card-note">{{ number_format($folders) }} folders</span></article>
<article class="card"><span class="card-label">Mailboxes</span><strong class="card-value">{{ number_format($mailboxes) }}</strong><span class="card-note">Managed email accounts</span></article>
<article class="card"><span class="card-label">Subdomains</span><strong class="card-value">{{ number_format($subdomains) }}</strong><span class="card-note">Managed hostnames</span></article>
<article class="card"><span class="card-label">Support</span><strong class="card-value">{{ number_format($openTickets) }}</strong><span class="card-note">Open requests</span></article>
</section>
<section class="section card"><h2>Secure workspace</h2><p>Current private storage usage: <strong>{{ number_format($storageBytes / 1073741824, 2) }} GB</strong>. Use Documents for files, Email for mailbox settings and Support for service requests.</p></section>
@endsection
