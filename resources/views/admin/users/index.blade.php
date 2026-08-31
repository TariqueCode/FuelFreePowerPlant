@extends('layouts.portal')

@section('title', 'User Management')
@section('eyebrow', 'ADMINISTRATION')
@section('heading', 'User Management')
@section('description', 'Create secure accounts and give each person only the responsibility they need.')

@section('content')
<div class="toolbar"><div class="user-summary"><strong>{{ $users->total() }}</strong><span>total account(s)</span></div>
    <a class="action" href="{{ route('admin.users.create') }}">+ Add account</a>
</div>
@if(session('status'))
    <div class="notice">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="notice error">{{ $errors->first() }}</div>
@endif
<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Person</th><th>Contact</th><th>Responsibility</th><th>Added</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>@php($role=$user->roles->first())<strong>{{ $role?->name ?: 'No role' }}</strong>@if($role?->description)<div class="role-note">{{ $role->description }}</div>@endif</td>
                    <td>{{ $user->created_at?->format('M d, Y') }}</td>
                    <td class="row-actions">
                        <a href="{{ route('admin.users.edit', $user) }}">Edit</a>
                        @if(!auth()->user()->is($user))
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Remove this account permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection

@push('styles')
<style>
.toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}.user-summary{display:flex;align-items:baseline;gap:7px;color:#b5cbd4}.user-summary strong{font-size:18px}.user-summary span{font-size:11px;color:#78919b}.action{display:inline-block;padding:11px 15px;border-radius:11px;background:#31afd2;color:#fff;text-decoration:none;font-weight:700;font-size:13px}.notice{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:rgba(67,194,137,.1);border:1px solid rgba(67,194,137,.2);color:#a8e5ca}.notice.error{background:rgba(210,65,65,.12);border-color:rgba(210,65,65,.2);color:#ffb0b0}.table-card{background:rgba(255,255,255,.025);border:1px solid rgba(110,200,235,.12);border-radius:18px;overflow:hidden}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:760px}th,td{text-align:left;padding:15px 17px;border-bottom:1px solid rgba(255,255,255,.06);font-size:13px}th{color:#74cce9;font-size:11px;letter-spacing:.08em;text-transform:uppercase}td{color:#b5cbd4}.role-note{font-size:11px;color:#718b95;margin-top:4px}.row-actions{display:flex;align-items:center;gap:12px}.row-actions a{color:#74cce9;text-decoration:none}.row-actions form{margin:0}.row-actions button{border:0;background:transparent;color:#ff9eaa;padding:0;cursor:pointer;font:inherit}.pagination{padding:12px}
</style>
@endpush
