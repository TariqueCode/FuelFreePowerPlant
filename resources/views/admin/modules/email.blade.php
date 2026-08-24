@extends('layouts.portal')

@section('title', 'Email Management')
@section('content')
<section class="hero">
    <div class="eyebrow">MANAGED EMAIL SERVICES</div>
    <h1>Email</h1>
    <p>Create, manage and connect FuelFree PowerPlant mailboxes across Android, Windows, Linux and standard IMAP/SMTP clients.</p>
</section>
<div class="grid">
    <article class="card"><span class="card-label">Mailboxes</span><strong class="card-value">—</strong><span class="card-note">Managed accounts.</span></article>
    <article class="card"><span class="card-label">Domains</span><strong class="card-value">—</strong><span class="card-note">Domain mailbox configuration.</span></article>
    <article class="card"><span class="card-label">Connections</span><strong class="card-value">—</strong><span class="card-note">IMAP / SMTP setup.</span></article>
</div>
<section class="section card"><h2>Mailbox management</h2><p>The email-management engine will be connected in the infrastructure phase. The dashboard is already permission-aware so only authorized accounts can access it.</p></section>
@endsection
