@extends('layouts.portal')
@section('title','Power Plants')
@section('content')
<div class="hero"><span class="eyebrow">ENERGY OPERATIONS</span><h1>Power Plants</h1><p>Manage facilities, technology, capacity and operational status from the central control panel.</p></div>
@if(session('success'))<div class="notice">{{ session('success') }}</div>@endif
<div class="toolbar"><a class="action" href="{{ route('admin.plants.create') }}">+ Add Power Plant</a></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Plant</th><th>Location</th><th>Technology</th><th>Capacity</th><th>Status</th><th></th></tr></thead><tbody>@forelse($plants as $plant)<tr><td><strong>{{ $plant->name }}</strong><br><small>{{ $plant->slug }}</small></td><td>{{ $plant->location ?: '—' }}</td><td>{{ $plant->technology ?: '—' }}</td><td>{{ $plant->capacity_kw !== null ? number_format($plant->capacity_kw, 3).' kW' : '—' }}</td><td>{{ ucfirst($plant->status) }}</td><td><a class="action" href="{{ route('admin.plants.edit',$plant) }}">Edit</a></td></tr>@empty<tr><td colspan="6">No power plants have been configured yet.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $plants->links() }}</div></div>
@endsection
