<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminModuleController extends Controller
{
    public function documents(): View
    {
        return view('admin.modules.documents');
    }

    public function email(): View
    {
        return view('admin.modules.email');
    }

    public function support(): View
    {
        return view('admin.modules.support');
    }
}
