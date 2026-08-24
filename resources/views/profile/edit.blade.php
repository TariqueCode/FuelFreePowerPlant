@extends('layouts.portal')

@section('title', 'My Profile')

@section('content')
<div class="hero">
    <div class="eyebrow">ACCOUNT CENTER</div>
    <h1>My Profile</h1>
    <p>Manage your personal information, password and account access from one secure place.</p>
</div>

@if(session('status'))
    <div class="notice">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="errors">
        <ul style="margin:0;padding-left:18px;line-height:1.7">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="profile-layout">
    <section class="profile-card">
        <div class="profile-avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div class="profile-summary">
            <strong>{{ $user->name }}</strong>
            <span>{{ $user->email }}</span>
            <small>{{ $user->roles->pluck('display_name')->filter()->join(', ') ?: $user->roles->pluck('name')->join(', ') }}</small>
        </div>
    </section>

    <form class="form-card profile-form" method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')
        <div class="section" style="margin-top:0"><h2>Personal information</h2></div>
        <div class="fields">
            <div>
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required>
            </div>
            <div>
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>
            </div>
            <div class="full profile-divider"></div>
            <div class="full"><div class="section" style="margin-top:0"><h2>Change password</h2><p>Leave these fields empty if you do not want to change your password.</p></div></div>
            <div>
                <label for="current_password">Current password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password">
            </div>
            <div>
                <label for="password">New password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" minlength="12">
            </div>
            <div>
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12">
            </div>
        </div>
        <div class="actions">
            <button type="submit">Save changes</button>
        </div>
    </form>

    <section class="profile-security">
        <div>
            <div class="eyebrow">ACCOUNT ACCESS</div>
            <h2>Sign out securely</h2>
            <p>Finished using the control center? Sign out here. You can sign in again from the secure login page.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="profile-signout" type="submit">Sign out</button>
        </form>
    </section>
</div>
@endsection

@push('styles')
<style>
.profile-layout{display:grid;grid-template-columns:minmax(0,.7fr) minmax(0,1.3fr);gap:16px;align-items:start}.profile-card,.profile-security{border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(9,38,54,.72),rgba(5,22,32,.82));padding:20px}.profile-card{display:flex;align-items:center;gap:15px}.profile-avatar{width:62px;height:62px;border-radius:18px;display:grid;place-items:center;background:linear-gradient(145deg,#31afd2,#167c9c);color:#fff;font-size:25px;font-weight:800;box-shadow:0 10px 30px rgba(49,175,210,.18)}.profile-summary{min-width:0}.profile-summary strong,.profile-summary span,.profile-summary small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.profile-summary strong{font-size:17px}.profile-summary span{margin-top:4px;color:#91adba;font-size:12px}.profile-summary small{margin-top:8px;color:#5fc7e8;font-size:9px;letter-spacing:.08em;text-transform:uppercase}.profile-form{grid-column:2;grid-row:1 / span 2;max-width:none}.profile-divider{height:1px;background:var(--line);margin:4px 0}.profile-security{grid-column:1;display:flex;flex-direction:column;gap:18px}.profile-security h2{font-size:17px;margin:6px 0}.profile-security p{color:var(--muted);font-size:12px;line-height:1.7;margin:0}.profile-signout{border:1px solid rgba(255,158,170,.22);background:rgba(255,100,120,.06);color:#ffb0b8;border-radius:11px;padding:11px 15px;cursor:pointer}.profile-signout:hover{background:rgba(255,100,120,.1);border-color:rgba(255,158,170,.4)}
@media(max-width:700px){.profile-layout{grid-template-columns:1fr}.profile-form{grid-column:auto;grid-row:auto}.profile-security{grid-column:auto}.profile-card{padding:16px}.profile-avatar{width:54px;height:54px;border-radius:16px}.profile-form{padding:18px}}
</style>
@endpush
