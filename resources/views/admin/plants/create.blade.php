@extends('layouts.portal')
@section('title','Add Power Plant')
@section('content')
<div class="hero"><span class="eyebrow">ENERGY OPERATIONS</span><h1>Add Power Plant</h1><p>Create a facility record first; verified performance data can be connected later through an approved data source.</p></div>
<div class="form-card"><form method="POST" action="{{ route('admin.plants.store') }}">@csrf @include('admin.plants.form')<div class="actions"><a href="{{ route('admin.plants.index') }}">Cancel</a><button type="submit">Create Plant</button></div></form></div>
@endsection
