<?php

namespace App\Http\Controllers;

use App\Support\Platform;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'platform' => Platform::name(),
            'user' => request()->user(),
        ]);
    }
}
