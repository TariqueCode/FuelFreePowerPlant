@extends('layouts.portal')
@section('title','Add Project — '.config('fuelfree.projects.label','Projects & Our Plans'))
@section('content')
<div class="hero"><span class="eyebrow">{{ config('fuelfree.projects.eyebrow','Our project portfolio') }}</span><h1>Add Project</h1><p>Create a facility record first; verified performance data can be connected later through an approved data source.</p></div>
<div class="form-card"><form method="POST" action="{{ route('admin.plants.store') }}">@csrf @include('admin.plants.form')<div class="actions"><a href="{{ route('admin.plants.index') }}">Cancel</a><button type="submit">Create Plant</button></div></form></div>
@endsection
