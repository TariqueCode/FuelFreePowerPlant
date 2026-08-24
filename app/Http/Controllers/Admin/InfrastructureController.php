<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\Subdomain;
use App\Models\User;
use App\Services\CpanelEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class InfrastructureController extends Controller
{
    public function email(Request $request): View
    {
        $isStaff = $request->user()->hasRole(['super-admin','administrator','project-manager','support-agent']);
        $accounts = EmailAccount::with('user')->when(!$isStaff, fn($q) => $q->where('user_id',$request->user()->id))->latest()->paginate(20);
        return view('admin.modules.email', compact('accounts','isStaff'));
    }

    public function createEmail(CpanelEmailService $cpanel): View
    {
        return view('admin.modules.email-form', [
            'account' => new EmailAccount(),
            'users' => User::orderBy('name')->get(),
            'cpanelConfigured' => $cpanel->configured(),
        ]);
    }

    public function storeEmail(Request $request, CpanelEmailService $cpanel): RedirectResponse
    {
        $rules = [
            'user_id' => ['required','integer','exists:users,id'], 'address' => ['required','email','max:255','unique:email_accounts,address'],
            'display_name' => ['nullable','string','max:150'], 'status' => ['required','in:active,suspended'],
            'imap_host' => ['nullable','string','max:255'], 'imap_port' => ['required','integer','min:1','max:65535'],
            'smtp_host' => ['nullable','string','max:255'], 'smtp_port' => ['required','integer','min:1','max:65535'],
            'username' => ['nullable','string','max:255'], 'password' => ['nullable','string','min:8','max:500'],
        ];
        if ($cpanel->configured()) $rules['password'][] = 'required';
        $data = $request->validate($rules);

        $provisioned = false;
        $providerMessage = 'Mailbox record saved. cPanel provisioning is not configured yet.';
        if ($cpanel->configured()) {
            try {
                $providerMessage = $cpanel->create($data['address'], $data['password']);
                $provisioned = true;
            } catch (Throwable $e) {
                report($e);
                return back()->withErrors(['address' => 'The mailbox could not be created on the mail server: '.$e->getMessage()])->withInput($request->except('password'));
            }
        }

        $data['provisioned'] = $provisioned;
        $data['provider_message'] = $providerMessage;
        if (! $data['username']) $data['username'] = $data['address'];
        if (! $data['imap_host']) $data['imap_host'] = config('cpanel.mail_host') ?: 'mail.'.(config('cpanel.domain') ?: $request->getHost());
        if (! $data['smtp_host']) $data['smtp_host'] = $data['imap_host'];
        EmailAccount::create($data);

        return redirect()->route('admin.email')->with('status', $provisioned ? 'Mailbox created successfully on the hosting mail server.' : 'Mailbox record saved. Add cPanel API settings to enable real mailbox provisioning.');
    }

    public function destroyEmail(EmailAccount $account, CpanelEmailService $cpanel): RedirectResponse
    {
        try {
            $message = $cpanel->delete($account);
            $account->delete();
            return back()->with('status', $message);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['email' => 'The mailbox could not be removed from the mail server: '.$e->getMessage()]);
        }
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
