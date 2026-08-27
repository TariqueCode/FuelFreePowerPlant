<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Services\WebmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class MailController extends Controller
{
    public function index(Request $request, WebmailService $webmail): View
    {
        $accounts = $this->accounts($request);
        $selected = $accounts->firstWhere('id', (int) $request->query('account')) ?: $accounts->first();
        $messages = [];
        $error = null;
        if ($selected) {
            try {
                $messages = $webmail->messages($selected->address, $selected->password, 50, $this->mailConfig($selected));
            } catch (Throwable $e) {
                report($e);
                $error = 'Mailbox connection failed. Check the account credentials and mail server settings.';
            }
        }
        return view('admin.mail.index', compact('accounts','selected','messages','error'));
    }

    public function storeAccount(Request $request, WebmailService $webmail): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('mail.manage'), 403);
        $data = $request->validate([
            'address' => ['required','email','max:255','ends_with:@fuelfreepowerplant.com'],
            'display_name' => ['nullable','string','max:150'],
            'mailbox_group' => ['required','in:general,info,career,other'],
            'password' => ['required','string','max:500'],
            'imap_host' => ['nullable','string','max:255'],
            'imap_port' => ['nullable','integer','between:1,65535'],
            'smtp_host' => ['nullable','string','max:255'],
            'smtp_port' => ['nullable','integer','between:1,65535'],
        ]);
        $data['address'] = strtolower($data['address']);
        $data['username'] = $data['address'];
        $data['imap_host'] = $data['imap_host'] ?: config('cpanel.mail_host');
        $data['imap_port'] = $data['imap_port'] ?: 993;
        $data['smtp_host'] = $data['smtp_host'] ?: config('cpanel.mail_host');
        $data['smtp_port'] = $data['smtp_port'] ?: 465;

        try {
            $webmail->login($data['address'], $data['password'], $this->mailConfigFromData($data));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['password' => 'The mailbox could not be verified. Check the address, password and server settings.'])->withInput($request->except('password'));
        }

        $data['user_id'] = $request->user()->id;
        $data['status'] = 'active';
        $data['provisioned'] = true;
        $data['provider_message'] = 'Verified by IMAP login.';
        EmailAccount::updateOrCreate(['address'=>$data['address']], $data);

        return redirect()->route('admin.mail',['account'=>EmailAccount::where('address',$data['address'])->value('id')])->with('status','Mailbox added and verified.');
    }

    public function show(Request $request, EmailAccount $emailAccount, WebmailService $webmail): View
    {
        $this->authorizeAccount($request, $emailAccount);
        $uid = (int) $request->route('uid');
        try {
            $message = $webmail->message($emailAccount->address, $emailAccount->password, $uid, $this->mailConfig($emailAccount));
        } catch (Throwable $e) {
            report($e);
            abort(404, 'Message could not be opened.');
        }
        return view('admin.mail.message', compact('emailAccount','message'));
    }

    public function compose(Request $request, EmailAccount $emailAccount, WebmailService $webmail): View
    {
        $this->authorizeAccount($request, $emailAccount);
        $initialTo = '';
        $initialSubject = '';
        $initialBody = '';
        if ($request->filled('reply')) {
            try {
                $message=$webmail->message($emailAccount->address,$emailAccount->password,(int)$request->query('reply'),$this->mailConfig($emailAccount));
                $initialTo=preg_match('/<([^>]+)>/',$message['from'],$m)?trim($m[1]):trim($message['from']);
                $initialSubject=str_starts_with(strtolower($message['subject']),'re:')?$message['subject']:'Re: '.$message['subject'];
                $initialBody='<p><br></p><hr><p><strong>Original message</strong><br>'.e($message['from']).'<br>'.e($message['date']).'</p><blockquote>'.$message['body'].'</blockquote>';
            } catch (Throwable $e) { report($e); }
        }
        return view('admin.mail.compose', compact('emailAccount','initialTo','initialSubject','initialBody'));
    }

    public function send(Request $request, EmailAccount $emailAccount, WebmailService $webmail): RedirectResponse
    {
        $this->authorizeAccount($request, $emailAccount);
        $data=$request->validate(['to'=>['required','email'],'subject'=>['nullable','string','max:255'],'body'=>['required','string','max:500000']]);
        try {
            $webmail->send($emailAccount->address,$emailAccount->password,$data['to'],$data['subject'] ?: '(No subject)',$data['body'],$this->mailConfig($emailAccount));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['send'=>'The message could not be sent.'])->withInput();
        }
        return redirect()->route('admin.mail',['account'=>$emailAccount->id])->with('status','Message sent successfully.');
    }

    private function accounts(Request $request)
    {
        $query=EmailAccount::query()->where('status','active')->orderBy('mailbox_group')->orderBy('address');
        if (!$request->user()->hasRole(['super-admin','mail-manager'])) $query->where('mailbox_group','career');
        return $query->get();
    }

    private function authorizeAccount(Request $request, EmailAccount $account): void
    {
        abort_unless($account->status==='active',404);
        if (!$request->user()->hasRole(['super-admin','mail-manager'])) abort_unless($account->mailbox_group==='career',403);
    }

    private function mailConfig(EmailAccount $account): array
    {
        return ['imap_host'=>$account->imap_host ?: config('cpanel.mail_host'),'imap_port'=>$account->imap_port ?: 993,'smtp_host'=>$account->smtp_host ?: config('cpanel.mail_host'),'smtp_port'=>$account->smtp_port ?: 465];
    }

    private function mailConfigFromData(array $data): array
    {
        return ['imap_host'=>$data['imap_host'],'imap_port'=>$data['imap_port'],'smtp_host'=>$data['smtp_host'],'smtp_port'=>$data['smtp_port']];
    }
}