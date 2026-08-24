@extends('layouts.portal')
@section('title','CMS')
@section('content')
<section class="hero"><div class="eyebrow">CONTENT MANAGEMENT</div><h1>CMS Pages</h1><p>Create and publish public pages without touching application code.</p></section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
<div class="toolbar"><a class="action" href="{{ route('admin.cms.create') }}">＋ New page</a></div>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Title</th><th>Slug</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead><tbody>
@forelse($pages as $page)<tr><td><strong>{{ $page->title }}</strong></td><td>/pages/{{ $page->slug }}</td><td>{{ $page->is_published ? 'Published' : 'Draft' }}</td><td>{{ $page->updated_at->format('M d, Y H:i') }}</td><td><a class="link" href="{{ route('admin.cms.edit',$page) }}">Edit</a> <form class="inline" method="POST" action="{{ route('admin.cms.destroy',$page) }}" onsubmit="return confirm('Delete this page?')">@csrf @method('DELETE')<button>Delete</button></form></td></tr>@empty<tr><td colspan="5">No CMS pages yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $pages->links() }}</div></div>
@endsection
@push('styles')<style>.toolbar{display:flex;justify-content:flex-end;margin-bottom:14px}.action{padding:11px 15px;border-radius:11px;background:#31afd2;color:#fff;text-decoration:none;font-weight:700;font-size:13px}.notice{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:rgba(67,194,137,.1);border:1px solid rgba(67,194,137,.2);color:#a8e5ca}.table-card{background:rgba(255,255,255,.025);border:1px solid var(--line);border-radius:18px;overflow:hidden}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:760px}th,td{text-align:left;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px}th{color:#74cce9;font-size:10px;text-transform:uppercase;letter-spacing:.08em}td{color:#b5cbd4}.link{color:#74cce9;text-decoration:none}.inline{display:inline}.inline button{border:0;background:transparent;color:#ff9eaa;cursor:pointer;font:inherit}.pagination{padding:12px}</style>@endpush
