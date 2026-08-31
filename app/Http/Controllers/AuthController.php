<?php

namespace App\Http\Controllers;

use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            Audit::record($request, 'login_failed', 'authentication', null, [
                'email' => $credentials['email'],
            ]);

            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        Audit::record($request, 'login', 'authentication');

        if ($request->hasSession() && $request->session()->has('url.intended')) {
            return redirect()->intended(route('dashboard'));
        }

        $user = $request->user();
        $destination = $user->hasPermission('dashboard.view') ? route('dashboard')
            : ($user->hasPermission('mail.view') ? route('admin.mail')
            : ($user->hasPermission('career.view') ? route('admin.career-applications.index')
            : route('profile')));

        return redirect()->to($destination);
    }

    public function logout(Request $request): RedirectResponse
    {
        Audit::record($request, 'logout', 'authentication');
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
