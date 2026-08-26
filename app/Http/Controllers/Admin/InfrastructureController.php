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
        $accounts = EmailAccount::latest()->paginate(20);
        return view('admin.modules.email', compact('accounts'));
    }

    public function createEmail(CpanelEmailService $cpanel): View
    {
        return view('admin.modules.email-form', [
            'account' => new EmailAccount(),
            'cpanelConfigured' => $cpanel->configured(),
            'domain' => config('cpanel.domain', 'fuelfreepowerplant.com'),
        ]);
    }

    public function storeEmail(Request $request, CpanelEmailService $cpanel): RedirectResponse
    {
        $domain = strtolower((string) config('cpanel.domain', 'fuelfreepowerplant.com'));
        $local = strtolower(trim((string) $request->input('local_part')));
        $address = $local.'@'.$domain;
        $data = $request->validate([
            'local_part' => ['required','string','max:64','regex:/^[a-z0-9](?:[a-z0-9._-]{0,62}[a-z0-9])?$/i'],
            'display_name' => ['nullable','string','max:150'],
            'password' => ['required','string','min:10','max:500','confirmed'],
        ]);
        if (EmailAccount::where('address',$address)->exists()) return back()->withErrors(['local_part'=>'That mailbox already exists.'])->withInput($request->except(['password','password_confirmation']));
        try {
            $providerMessage = $cpanel->create($address, $data['password']);
            EmailAccount::create([
                'user_id' => $request->user()->id,
                'address' => $address,
                'display_name' => $data['display_name'] ?: null,
                'status' => 'active',
                'provisioned' => true,
                'provider_message' => $providerMessage,
                'imap_host' => config('cpanel.mail_host', 'mail.'.$domain),
                'imap_port' => 993,
                'smtp_host' => config('cpanel.mail_host', 'mail.'.$domain),
                'smtp_port' => 465,
                'username' => $address,
                'password' => $data['password'],
            ]);
            return redirect()->route('admin.email')->with('status', 'Mailbox '.$address.' created successfully.');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['local_part'=>'The mailbox could not be created: '.$e->getMessage()])->withInput($request->except(['password','password_confirmation']));
        }
    }

    public function changeEmailPassword(Request $request, EmailAccount $account, CpanelEmailService $cpanel): RedirectResponse
    {
        $data = $request->validate(['password'=>['required','string','min:10','max:500','confirmed']]);
        try {
            $message = $cpanel->changePassword($account, $data['password']);
            $account->update(['password'=>$data['password'], 'provider_message'=>$message, 'provisioned'=>true]);
            return back()->with('status', 'Password changed for '.$account->address.'.');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['email'=>'Password could not be changed: '.$e->getMessage()]);
        }
    }

    public function toggleEmailStatus(EmailAccount $account, CpanelEmailService $cpanel): RedirectResponse
    {
        $active = $account->status !== 'active';
        try {
            $message = $cpanel->setActive($account, $active);
            $account->update(['status'=>$active ? 'active' : 'suspended', 'provider_message'=>$message]);
            return back()->with('status', $account->address.' is now '.($active ? 'active.' : 'inactive.'));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['email'=>'Mailbox status could not be changed: '.$e->getMessage()]);
        }
    }

    public function destroyEmail(EmailAccount $account, CpanelEmailService $cpanel): RedirectResponse
    {
        try {
            $message = $cpanel->delete($account);
            $account->delete();
            return back()->with('status', $message);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['email'=>'The mailbox could not be removed from the mail server: '.$e->getMessage()]);
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
