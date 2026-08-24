<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\Subdomain;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InfrastructureController extends Controller
{
    public function email(Request $request): View
    {
        $isStaff = $request->user()->hasRole(['super-admin','administrator','project-manager','support-agent']);
        $accounts = EmailAccount::with('user')->when(!$isStaff, fn($q) => $q->where('user_id',$request->user()->id))->latest()->paginate(20);
        return view('admin.modules.email', compact('accounts','isStaff'));
    }

    public function createEmail(): View
    {
        return view('admin.modules.email-form', ['account' => new EmailAccount(), 'users' => User::orderBy('name')->get()]);
    }

    public function storeEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required','integer','exists:users,id'], 'address' => ['required','email','max:255','unique:email_accounts,address'],
            'display_name' => ['nullable','string','max:150'], 'status' => ['required','in:active,suspended'],
            'imap_host' => ['nullable','string','max:255'], 'imap_port' => ['required','integer','min:1','max:65535'],
            'smtp_host' => ['nullable','string','max:255'], 'smtp_port' => ['required','integer','min:1','max:65535'],
            'username' => ['nullable','string','max:255'], 'password' => ['nullable','string','max:500'],
        ]);
        EmailAccount::create($data);
        return redirect()->route('admin.email')->with('status','Mailbox configuration saved. Connect it to the hosting mail service to provision the real mailbox.');
    }

    public function destroyEmail(EmailAccount $account): RedirectResponse
    {
        $account->delete(); return back()->with('status','Mailbox record removed.');
    }

    public function subdomains(Request $request): View
    {
        $isStaff = $request->user()->hasRole(['super-admin','administrator','project-manager','support-agent']);
        $subdomains = Subdomain::with('user')->when(!$isStaff, fn($q) => $q->where('user_id',$request->user()->id))->latest()->paginate(20);
        return view('admin.modules.subdomains', compact('subdomains','isStaff'));
    }

    public function createSubdomain(): View
    {
        return view('admin.modules.subdomain-form', ['subdomain' => new Subdomain(), 'users' => User::orderBy('name')->get()]);
    }

    public function storeSubdomain(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
            'name' => ['required','string','max:180','regex:/^(?=.{1,180}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i','unique:subdomains,name'],
            'target' => ['nullable','string','max:500'], 'status' => ['required','in:active,suspended'], 'ssl_enabled' => ['nullable','boolean'],
        ]);
        $data['ssl_enabled'] = $request->boolean('ssl_enabled');
        Subdomain::create($data);
        return redirect()->route('admin.subdomains')->with('status','Subdomain record saved. DNS/web-server provisioning is the next infrastructure step.');
    }

    public function destroySubdomain(Subdomain $subdomain): RedirectResponse
    {
        $subdomain->delete(); return back()->with('status','Subdomain record removed.');
    }
}
