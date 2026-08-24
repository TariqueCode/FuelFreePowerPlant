@extends('layouts.portal')

@section('title', 'User Management')
@section('eyebrow', 'ADMINISTRATION')
@section('heading', 'User Management')
@section('description', 'Create and manage secure staff and client accounts.')

@section('content')
<div class="toolbar">
    <a class="action" href="{{ route('admin.users.create') }}">+ Create user</a>
</div>
@if(session('status'))
    <div class="notice">{{ session('status') }}</div>
@endif
<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roles->pluck('name')->join(', ') ?: 'No role' }}</td>
                    <td>{{ $user->created_at?->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection

@push('styles')
<style>
.toolbar{display:flex;justify-content:flex-end;margin-bottom:14px}.action{display:inline-block;padding:11px 15px;border-radius:11px;background:#31afd2;color:#fff;text-decoration:none;font-weight:700;font-size:13px}.notice{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:rgba(67,194,137,.1);border:1px solid rgba(67,194,137,.2);color:#a8e5ca}.table-card{background:rgba(255,255,255,.025);border:1px solid rgba(110,200,235,.12);border-radius:18px;overflow:hidden}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:620px}th,td{text-align:left;padding:15px 17px;border-bottom:1px solid rgba(255,255,255,.06);font-size:13px}th{color:#74cce9;font-size:11px;letter-spacing:.08em;text-transform:uppercase}td{color:#b5cbd4}.pagination{padding:12px}
</style>
@endpush
