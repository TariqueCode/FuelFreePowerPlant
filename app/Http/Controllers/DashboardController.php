<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = request()->user();

        if ($user->hasRole('client')) {
            return redirect()->route('portal.dashboard');
        }

        return redirect()->route('admin.dashboard');
    }
}
