@extends('webmail.layout')

@section('content')
<div class="login-wrap">
    <section class="login-card">
        <div class="brand"><span class="brand-mark">✉</span><span>FuelFree PowerPlant</span></div>
        <h1>Webmail</h1>
        <p class="sub">Sign in to your company mailbox.</p>
        @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('webmail.login.store') }}" autocomplete="on">
            @csrf
            <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@fuelfreepowerplant.com" required autofocus autocomplete="username"></div>
            <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" placeholder="Your mailbox password" required autocomplete="current-password"></div>
            <button class="btn primary full" type="submit">Sign in to Webmail</button>
        </form>
        <p class="hint">Use the mailbox credentials provided by FuelFree PowerPlant. This login is separate from the website administration account.</p>
    </section>
</div>
@endsection
