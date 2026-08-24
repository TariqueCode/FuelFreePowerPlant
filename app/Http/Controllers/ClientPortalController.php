<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ClientPortalController extends Controller
{
    public function __invoke(): View
    {
        return view('portal.dashboard');
    }
}
