@extends('layouts.portal')

@section('title', 'Documents')
@section('content')
<section class="hero">
    <div class="eyebrow">SECURE DOCUMENT VAULT</div>
    <h1>Documents</h1>
    <p>Manage protected company documents, project files and client-accessible records from one secure workspace.</p>
</section>
<div class="grid">
    <article class="card"><span class="card-label">Files</span><strong class="card-value">—</strong><span class="card-note">Document inventory will connect here.</span></article>
    <article class="card"><span class="card-label">Protected</span><strong class="card-value">—</strong><span class="card-note">Encrypted access controls.</span></article>
    <article class="card"><span class="card-label">Shared</span><strong class="card-value">—</strong><span class="card-note">Client sharing workflow.</span></article>
</div>
<section class="section card"><h2>Vault module</h2><p>The secure document infrastructure will be connected here in the Document Vault phase. Your current role and permissions already protect this area.</p></section>
@endsection
