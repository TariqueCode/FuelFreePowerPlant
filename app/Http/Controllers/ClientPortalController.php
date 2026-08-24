<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\EmailAccount;
use App\Models\Subdomain;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $documents = Document::where('user_id', $user->id)->count();
        $folders = DocumentFolder::where('user_id', $user->id)->count();
        $mailboxes = EmailAccount::where('user_id', $user->id)->count();
        $subdomains = Subdomain::where('user_id', $user->id)->count();
        $openTickets = SupportTicket::where('user_id', $user->id)->whereIn('status', ['open','in-progress'])->count();
        $storageBytes = collect(Storage::disk('local')->allFiles("private/{$user->id}"))->reject(fn(string $path) => str_contains($path, '/.uploads/'))->sum(fn(string $path) => (int) Storage::disk('local')->size($path));

        return view('portal.dashboard', compact('documents','folders','mailboxes','subdomains','openTickets','storageBytes'));
    }
}
