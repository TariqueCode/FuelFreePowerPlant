@extends('layouts.portal')
@section('title','Inquiries')
@section('content')
<div class="hero"><div class="eyebrow">CLIENT COMMUNICATION</div><h1>Inquiries</h1><p>Review and manage messages submitted through the public website.</p></div>
<div class="toolbar"><a class="action" href="{{ route('admin.inquiries.index') }}"><i class="fa-solid fa-inbox"></i> All</a><a class="action" href="{{ route('admin.inquiries.index',['status'=>'new']) }}"><i class="fa-solid fa-circle-dot"></i> New</a><a class="action" href="{{ route('admin.inquiries.index',['status'=>'in_progress']) }}"><i class="fa-solid fa-spinner"></i> In progress</a></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Name</th><th>Subject</th><th>Email</th><th>Status</th><th>Received</th><th></th></tr></thead><tbody>@forelse($inquiries as $inquiry)<tr><td>{{ $inquiry->name }}</td><td>{{ $inquiry->subject }}</td><td>{{ $inquiry->email }}</td><td>{{ str_replace('_',' ',ucfirst($inquiry->status)) }}</td><td>{{ $inquiry->created_at->format('d M Y, h:i A') }}</td><td><a class="action" href="{{ route('admin.inquiries.show',$inquiry) }}">View</a></td></tr>@empty<tr><td colspan="6">No inquiries found.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $inquiries->links() }}</div></div>
@endsection
