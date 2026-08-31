@extends('layouts.portal')

@section('title', 'Edit User')

@section('content')
<section class="hero">
    <div class="eyebrow">USER MANAGEMENT</div>
    <h1>Edit user</h1>
    <p>Update this account using plain-language controls. The selected responsibility determines what this person can manage.</p>
</section>

@if($errors->any())
    <div class="errors">{{ $errors->first() }}</div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PATCH')
        <div class="fields">
            <div>
                <label for="name">Full name</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="120">
            </div>
            <div>
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255">
            </div>
            <div>
                <label for="role_id">Responsibility</label>
                <select id="role_id" name="role_id" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id', $user->roles->first()?->id) == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <div id="capabilityHint" class="capability-hint">Choose a responsibility to see what this account can manage.</div>
            </div>
            <div>
                <label for="password">New password</label>
                <input id="password" type="password" name="password" minlength="12" autocomplete="new-password">
            </div>
            <div>
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" minlength="12" autocomplete="new-password">
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('admin.users.index') }}">Cancel</a>
            <button type="submit">Save changes</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')<script>
const roleCapabilities=@json($roles->mapWithKeys(fn($r)=>[$r->id=>$r->permissions->pluck('name')->values()]));
const roleSelect=document.getElementById('role_id'),hint=document.getElementById('capabilityHint');
function showCaps(){const caps=roleCapabilities[roleSelect.value]||[];hint.innerHTML=caps.length?'<strong>This account can manage:</strong><ul>'+caps.map(c=>'<li>'+String(c).replace(/[&<>]/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[m]))+'</li>').join('')+'</ul>':'Choose a responsibility to see what this account can manage.'}
roleSelect.addEventListener('change',showCaps);showCaps();
</script>@endpush
