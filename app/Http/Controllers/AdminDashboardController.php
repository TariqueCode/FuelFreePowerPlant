<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $isFullAdmin = $user->hasRole(['super-admin', 'administrator']);
        $users = $isFullAdmin ? User::count() : null;
        $documents = Document::where('user_id', $user->id)->count();
        $folders = DocumentFolder::where('user_id', $user->id)->count();
        $storageBytes = collect(Storage::disk('local')->allFiles("private/{$user->id}"))
            ->reject(fn (string $path) => str_contains($path, '/.uploads/'))
            ->sum(fn (string $path) => (int) Storage::disk('local')->size($path));

        return view('admin.dashboard', compact('users', 'documents', 'folders', 'storageBytes'));
    }
}
