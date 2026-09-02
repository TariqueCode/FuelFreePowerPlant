@extends('layouts.portal')
@section('title','Edit Project — '.config('fuelfree.projects.label','Projects & Our Plans'))
@section('content')
<div class="hero"><span class="eyebrow">{{ config('fuelfree.projects.eyebrow','Our project portfolio') }}</span><h1>Edit Project</h1><p>Update facility metadata without changing its historical performance records.</p></div>
<div class="form-card"><form method="POST" action="{{ route('admin.plants.update',$plant) }}">@csrf @method('PATCH') @include('admin.plants.form',['plant'=>$plant])<div class="actions"><a href="{{ route('admin.plants.index') }}">Cancel</a><button type="submit">Save Changes</button></div></form></div>
@endsection
