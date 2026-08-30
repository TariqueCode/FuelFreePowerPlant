@extends('layouts.portal')

@section('title', 'My Profile')

@section('content')
<div class="hero profile-hero">
    <div class="eyebrow">ACCOUNT CENTER</div>
    <div class="hero-heading-row">
        <div>
            <h1>My Profile</h1>
            <p>Manage your personal information, password and account access from one secure place.</p>
        </div>
        <a class="profile-dashboard-link" href="{{ route('admin.dashboard') }}">
            <i class="fa-solid fa-arrow-left"></i><span>Back to dashboard</span>
        </a>
    </div>
</div>

@if(session('status'))
    <div class="notice profile-notice" role="status"><i class="fa-solid fa-circle-check"></i><span>{{ session('status') }}</span></div>
@endif

@if($errors->any())
    <div class="errors profile-errors" role="alert">
        <div class="profile-alert-title"><i class="fa-solid fa-triangle-exclamation"></i><strong>Please check the highlighted fields</strong></div>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="profile-layout">
    <aside class="profile-side">
        <section class="profile-card">
            <div class="profile-avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div class="profile-summary">
                <strong>{{ $user->name }}</strong>
                <span>{{ $user->email }}</span>
                <small>{{ $user->roles->pluck('display_name')->filter()->join(', ') ?: $user->roles->pluck('name')->join(', ') }}</small>
            </div>
        </section>

        <section class="profile-access-card">
            <div class="eyebrow">ACCOUNT ACCESS</div>
            <div class="access-row"><span><i class="fa-solid fa-shield-halved"></i> Access level</span><strong>{{ $user->roles->pluck('display_name')->filter()->join(', ') ?: $user->roles->pluck('name')->join(', ') }}</strong></div>
            <div class="access-row"><span><i class="fa-solid fa-lock"></i> Password</span><strong>Protected</strong></div>
            <div class="access-row"><span><i class="fa-solid fa-circle-check"></i> Account</span><strong>Active</strong></div>
        </section>

        <section class="profile-security">
            <div>
                <div class="eyebrow">SECURE SESSION</div>
                <h2>Sign out securely</h2>
                <p>Finished using the control center? Sign out here. You can sign in again from the secure login page.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="profile-signout" type="submit"><i class="fa-solid fa-right-from-bracket"></i> Sign out</button>
            </form>
        </section>
    </aside>

    <form class="form-card profile-form" method="POST" action="{{ route('profile.update') }}" novalidate>
        @csrf
        @method('PATCH')

        <div class="form-section">
            <div class="section-heading">
                <span class="section-icon"><i class="fa-solid fa-user"></i></span>
                <div><h2>Personal information</h2><p>Keep the name and email used across your administration account up to date.</p></div>
            </div>

            <div class="fields">
                <div class="field">
                    <label for="name">Full name</label>
                    <div class="input-wrap"><i class="fa-solid fa-user"></i><input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" maxlength="120" required></div>
                </div>
                <div class="field">
                    <label for="email">Email address</label>
                    <div class="input-wrap"><i class="fa-solid fa-envelope"></i><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" maxlength="255" required></div>
                </div>
            </div>
        </div>

        <div class="form-section password-section">
            <div class="section-heading">
                <span class="section-icon"><i class="fa-solid fa-key"></i></span>
                <div><h2>Change password</h2><p>Use a strong password with at least 12 characters. Leave all password fields empty to keep your current password.</p></div>
            </div>

            <div class="fields password-fields">
                <div class="field full">
                    <label for="current_password">Current password</label>
                    <div class="input-wrap password-wrap"><i class="fa-solid fa-lock"></i><input id="current_password" name="current_password" type="password" autocomplete="current-password"><button type="button" class="password-toggle" data-target="current_password" aria-label="Show current password"><i class="fa-solid fa-eye"></i></button></div>
                    <small class="field-hint">Required only when changing your password.</small>
                </div>
                <div class="field">
                    <label for="password">New password</label>
                    <div class="input-wrap password-wrap"><i class="fa-solid fa-key"></i><input id="password" name="password" type="password" autocomplete="new-password" minlength="12"><button type="button" class="password-toggle" data-target="password" aria-label="Show new password"><i class="fa-solid fa-eye"></i></button></div>
                    <div class="password-meter" aria-live="polite"><span></span><span></span><span></span><span></span></div>
                    <small class="field-hint" id="password-hint">12+ characters recommended.</small>
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirm new password</label>
                    <div class="input-wrap password-wrap"><i class="fa-solid fa-check"></i><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12"><button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Show password confirmation"><i class="fa-solid fa-eye"></i></button></div>
                    <small class="field-hint" id="match-hint"></small>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button class="primary-action" type="submit"><i class="fa-solid fa-floppy-disk"></i><span>Save changes</span></button>
            <button class="secondary-action" type="reset"><i class="fa-solid fa-rotate-left"></i><span>Reset</span></button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.profile-hero{margin-bottom:24px}.hero-heading-row{display:flex;align-items:flex-end;justify-content:space-between;gap:20px}.profile-dashboard-link{display:inline-flex;align-items:center;gap:8px;flex:0 0 auto;border:1px solid var(--line);border-radius:12px;padding:10px 14px;color:#a9cbd5;background:rgba(67,194,229,.045);text-decoration:none;font-size:12px}.profile-dashboard-link:hover{color:#eaf8fb;border-color:rgba(104,204,235,.28);background:rgba(67,194,229,.08)}.profile-notice,.profile-errors{display:flex;gap:10px;align-items:flex-start;margin:0 0 18px;padding:13px 15px;border-radius:13px}.profile-notice{color:#b7f0d1;background:rgba(63,200,132,.07);border:1px solid rgba(63,200,132,.18)}.profile-errors{display:block;color:#ffd1d6;background:rgba(255,100,120,.06);border:1px solid rgba(255,100,120,.18)}.profile-alert-title{display:flex;align-items:center;gap:9px}.profile-errors ul{margin:8px 0 0;padding-left:20px;line-height:1.7;color:#ffb8c0}.profile-layout{display:grid;grid-template-columns:minmax(245px,.68fr) minmax(0,1.32fr);gap:18px;align-items:start}.profile-side{display:flex;flex-direction:column;gap:14px;min-width:0}.profile-card,.profile-access-card,.profile-security{border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(9,38,54,.72),rgba(5,22,32,.82));padding:18px}.profile-card{display:flex;align-items:center;gap:14px}.profile-avatar{width:62px;height:62px;border-radius:18px;display:grid;place-items:center;flex:0 0 62px;background:linear-gradient(145deg,#31afd2,#167c9c);color:#fff;font-size:25px;font-weight:800;box-shadow:0 10px 30px rgba(49,175,210,.18)}.profile-summary{min-width:0}.profile-summary strong,.profile-summary span,.profile-summary small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.profile-summary strong{font-size:17px}.profile-summary span{margin-top:4px;color:#91adba;font-size:12px}.profile-summary small{margin-top:8px;color:#5fc7e8;font-size:9px;letter-spacing:.08em;text-transform:uppercase}.profile-access-card .eyebrow{margin-bottom:12px}.access-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 0;border-top:1px solid rgba(104,204,235,.08);font-size:11px}.access-row:first-of-type{border-top:0}.access-row span{color:#87a7b4}.access-row span i{width:18px;color:#5fc9e8}.access-row strong{color:#d7edf1;text-align:right;font-size:11px}.profile-security{display:flex;flex-direction:column;gap:16px}.profile-security h2{font-size:17px;margin:6px 0}.profile-security p{color:var(--muted);font-size:12px;line-height:1.7;margin:0}.profile-signout{border:1px solid rgba(255,158,170,.22);background:rgba(255,100,120,.06);color:#ffb0b8;border-radius:11px;padding:11px 15px;cursor:pointer}.profile-signout:hover{background:rgba(255,100,120,.1);border-color:rgba(255,158,170,.4)}.profile-form{max-width:none;min-width:0;padding:0;overflow:hidden}.form-section{padding:22px}.form-section+.form-section{border-top:1px solid var(--line)}.section-heading{display:flex;gap:12px;align-items:flex-start;margin-bottom:20px}.section-icon{width:38px;height:38px;flex:0 0 38px;border-radius:11px;display:grid;place-items:center;color:#67d3ed;background:rgba(67,194,229,.08);border:1px solid rgba(104,204,235,.1)}.section-heading h2{margin:0;font-size:19px}.section-heading p{margin:6px 0 0;color:var(--muted);font-size:11px;line-height:1.65}.fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.field{min-width:0}.field.full{grid-column:1/-1}.field label{display:block;margin-bottom:7px;color:#b8d3db;font-size:11px;font-weight:600}.input-wrap{display:flex;align-items:center;min-width:0;border:1px solid rgba(104,204,235,.13);border-radius:12px;background:rgba(3,16,25,.48);transition:border-color .18s,box-shadow .18s,background .18s}.input-wrap:focus-within{border-color:rgba(104,204,235,.42);box-shadow:0 0 0 3px rgba(67,194,229,.07);background:rgba(3,16,25,.7)}.input-wrap>i{width:40px;flex:0 0 40px;text-align:center;color:#5ebed7;font-size:12px}.input-wrap input{width:100%;min-width:0;border:0;outline:0;background:transparent;color:var(--text);padding:12px 10px 12px 0;font-size:13px}.password-toggle{width:40px;height:40px;flex:0 0 40px;border:0;background:transparent;color:#7598a6;cursor:pointer}.password-toggle:hover{color:#bde6ee}.field-hint{display:block;margin-top:6px;color:#718f9c;font-size:10px;line-height:1.5}.password-meter{display:flex;gap:4px;margin-top:8px}.password-meter span{height:3px;flex:1;border-radius:99px;background:rgba(104,204,235,.1);transition:background .18s}.form-actions{display:flex;align-items:center;gap:9px;padding:18px 22px;border-top:1px solid var(--line);background:rgba(2,13,21,.32)}.primary-action,.secondary-action{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:11px;padding:11px 15px;cursor:pointer;font-size:12px;font-weight:700}.primary-action{border:1px solid rgba(104,204,235,.25);background:linear-gradient(135deg,#24a9ce,#167f9e);color:#fff}.secondary-action{border:1px solid var(--line);background:rgba(67,194,229,.035);color:#9ab9c4}.primary-action:hover{filter:brightness(1.08)}.secondary-action:hover{color:#eaf8fb;background:rgba(67,194,229,.07)}
@media(max-width:900px){.profile-layout{grid-template-columns:1fr}.profile-side{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr)}.profile-card{grid-column:1/-1}.profile-security{grid-column:1/-1}.profile-form{grid-column:1}.hero-heading-row{align-items:flex-start;flex-direction:column}.profile-dashboard-link{width:100%;justify-content:center}}
@media(max-width:600px){.profile-hero{margin-bottom:18px}.profile-hero h1{font-size:36px}.profile-hero p{font-size:15px;line-height:1.55}.profile-layout{gap:12px}.profile-side{display:flex;gap:12px}.profile-card,.profile-access-card,.profile-security{border-radius:16px;padding:16px}.profile-card{gap:12px}.profile-avatar{width:56px;height:56px;flex-basis:56px;border-radius:16px}.profile-summary strong{font-size:16px}.profile-summary span{font-size:11px}.form-section{padding:18px 16px}.section-heading{margin-bottom:16px}.section-icon{width:34px;height:34px;flex-basis:34px;border-radius:10px}.section-heading h2{font-size:18px}.fields{grid-template-columns:1fr;gap:14px}.field.full{grid-column:auto}.form-actions{padding:15px 16px;position:sticky;bottom:0;z-index:5;backdrop-filter:blur(14px)}.primary-action,.secondary-action{flex:1}.profile-dashboard-link{padding:11px 13px}.access-row{font-size:10px}.access-row strong{font-size:10px}}
</style>
@endpush

@push('head')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const input = document.getElementById(toggle.dataset.target);
            if (!input) return;
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            toggle.innerHTML = showing ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
        });
    });

    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    const meter = document.querySelector('.password-meter');
    const hint = document.getElementById('password-hint');
    const matchHint = document.getElementById('match-hint');

    const updatePasswordUi = () => {
        if (!password || !meter) return;
        const value = password.value;
        const score = [
            value.length >= 12,
            /[A-Z]/.test(value),
            /[0-9]/.test(value),
            /[^A-Za-z0-9]/.test(value)
        ].filter(Boolean).length;
        meter.querySelectorAll('span').forEach((bar, index) => bar.style.background = index < score ? 'rgba(63,200,132,.72)' : 'rgba(104,204,235,.1)');
        if (!value) hint.textContent = '12+ characters recommended.';
        else if (score < 3) hint.textContent = 'Use 12+ characters with uppercase, numbers and a symbol.';
        else if (score === 3) hint.textContent = 'Good password. Add a symbol for extra strength.';
        else hint.textContent = 'Strong password.';
        if (confirmation && matchHint) {
            if (!confirmation.value) matchHint.textContent = '';
            else matchHint.textContent = confirmation.value === value ? 'Passwords match.' : 'Passwords do not match.';
            matchHint.style.color = confirmation.value && confirmation.value === value ? '#78d9a3' : '#ff9ca8';
        }
    };
    password?.addEventListener('input', updatePasswordUi);
    confirmation?.addEventListener('input', updatePasswordUi);
});
</script>
@endpush