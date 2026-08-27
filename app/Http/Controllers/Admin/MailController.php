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
        $folder = $request->query('folder', 'INBOX');
        $folders = [];
        $messages = [];
        $error = null;

        if ($selected) {
            try {
                $folders = $webmail->folders($selected->address, $selected->password, $this->mailConfig($selected));
                if (!collect($folders)->contains(fn ($f) => $f['name'] === $folder)) {
                    $folder = 'INBOX';
                }
                $messages = $webmail->messages($selected->address, $selected->password, 50, $this->mailConfig($selected), $folder);
            } catch (Throwable $e) {
                report($e);
                $error = $e->getMessage() ?: 'Mailbox connection failed. Check the account credentials and mail server settings.';
            }
        }

        return view('admin.mail.index', compact('accounts', 'selected', 'folders', 'folder', 'messages', 'error'));
    }

    public function storeAccount(Request $request, WebmailService $webmail): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('mail.manage'), 403);

        $data = $request->validate([
            'address' => ['required', 'email', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:150'],
            'provider' => ['required', 'in:cpanel,gmail,outlook,custom'],
            'password' => ['required', 'string', 'max:1000'],
            'imap_host' => ['nullable', 'string', 'max:255'],
            'imap_port' => ['nullable', 'integer', 'between:1,65535'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'between:1,65535'],
        ]);

        $data['address'] = strtolower(trim($data['address']));
        $data['username'] = $data['address'];
        $preset = $this->providerPreset($data['provider']);
        $data['imap_host'] = $data['imap_host'] ?: $preset['imap_host'];
        $data['imap_port'] = $data['imap_port'] ?: $preset['imap_port'];
        $data['smtp_host'] = $data['smtp_host'] ?: $preset['smtp_host'];
        $data['smtp_port'] = $data['smtp_port'] ?: $preset['smtp_port'];

        if ($data['provider'] === 'cpanel' && !$data['imap_host']) {
            $data['imap_host'] = config('cpanel.mail_host', 'mail.'.parse_url(config('app.url'), PHP_URL_HOST));
        }
        if ($data['provider'] === 'cpanel' && !$data['smtp_host']) {
            $data['smtp_host'] = config('cpanel.mail_host', 'mail.'.parse_url(config('app.url'), PHP_URL_HOST));
        }

        try {
            $webmail->login($data['address'], $data['password'], $this->mailConfigFromData($data));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['password' => 'Mailbox verification failed: '.($e->getMessage() ?: 'check the email, password and server settings.')])->withInput($request->except('password'));
        }

        $account = EmailAccount::updateOrCreate(
            ['address' => $data['address']],
            [
                'user_id' => $request->user()->id,
                'address' => $data['address'],
                'display_name' => $data['display_name'] ?: $data['address'],
                'status' => 'active',
                'imap_host' => $data['imap_host'],
                'imap_port' => $data['imap_port'],
                'smtp_host' => $data['smtp_host'],
                'smtp_port' => $data['smtp_port'],
                'username' => $data['username'],
                'password' => $data['password'],
                'provisioned' => true,
                'provider_message' => 'Verified by IMAP login ('.$data['provider'].').',
            ]
        );

        return redirect()->route('admin.mail', ['account' => $account->id])->with('status', 'Mailbox connected and verified successfully.');
    }

    public function toggle(Request $request, EmailAccount $emailAccount): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('mail.manage'), 403);
        $emailAccount->update(['status' => $emailAccount->status === 'active' ? 'inactive' : 'active']);
        return back()->with('status', $emailAccount->status === 'active' ? 'Mailbox activated.' : 'Mailbox deactivated.');
    }

    public function destroy(Request $request, EmailAccount $emailAccount): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('mail.manage'), 403);
        $emailAccount->delete();
        return redirect()->route('admin.mail')->with('status', 'Mailbox removed from Help Desk.');
    }

    public function show(Request $request, EmailAccount $emailAccount, WebmailService $webmail): View
    {
        $this->authorizeAccount($request, $emailAccount);
        $folder = $request->query('folder', 'INBOX');
        $uid = (int) $request->route('uid');

        try {
            $message = $webmail->message($emailAccount->address, $emailAccount->password, $uid, $this->mailConfig($emailAccount), $folder);
        } catch (Throwable $e) {
            report($e);
            abort(404, 'Message could not be opened.');
        }

        return view('admin.mail.message', compact('emailAccount', 'message', 'folder'));
    }

    public function compose(Request $request, EmailAccount $emailAccount, WebmailService $webmail): View
    {
        $this->authorizeAccount($request, $emailAccount);
        $initialTo = '';
        $initialSubject = '';
        $initialBody = '';

        if ($request->filled('reply')) {
            try {
                $message = $webmail->message($emailAccount->address, $emailAccount->password, (int) $request->query('reply'), $this->mailConfig($emailAccount), $request->query('folder', 'INBOX'));
                $initialTo = preg_match('/<([^>]+)>/', $message['from'], $m) ? trim($m[1]) : trim($message['from']);
                $initialSubject = str_starts_with(strtolower($message['subject']), 're:') ? $message['subject'] : 'Re: '.$message['subject'];
                $initialBody = '<p><br></p><hr><p><strong>Original message</strong><br>'.e($message['from']).'<br>'.e($message['date']).'</p><blockquote>'.$message['body'].'</blockquote>';
            } catch (Throwable $e) {
                report($e);
            }
        }

        return view('admin.mail.compose', compact('emailAccount', 'initialTo', 'initialSubject', 'initialBody'));
    }

    public function send(Request $request, EmailAccount $emailAccount, WebmailService $webmail): RedirectResponse
    {
        $this->authorizeAccount($request, $emailAccount);
        $data = $request->validate([
            'to' => ['required', 'email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:500000'],
        ]);

        try {
            $webmail->send($emailAccount->address, $emailAccount->password, $data['to'], $data['subject'] ?: '(No subject)', $data['body'], $this->mailConfig($emailAccount));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['send' => 'The message could not be sent: '.($e->getMessage() ?: 'SMTP error.')])->withInput();
        }

        return redirect()->route('admin.mail', ['account' => $emailAccount->id, 'folder' => 'Sent'])->with('status', 'Message sent successfully.');
    }

    private function accounts(Request $request)
    {
        return EmailAccount::query()
            ->when(!$request->user()->hasRole(['super-admin', 'mail-manager']), fn ($q) => $q->where('status', 'active')->where('address', 'like', 'career@%'))
            ->orderByRaw("CASE WHEN status='active' THEN 0 ELSE 1 END")
            ->orderBy('address')
            ->get();
    }

    private function authorizeAccount(Request $request, EmailAccount $account): void
    {
        abort_unless($account->status === 'active', 404);
        if (!$request->user()->hasRole(['super-admin', 'mail-manager'])) {
            abort_unless(str_starts_with($account->address, 'career@'), 403);
        }
    }

    private function mailConfig(EmailAccount $account): array
    {
        return [
            'imap_host' => $account->imap_host ?: config('cpanel.mail_host'),
            'imap_port' => $account->imap_port ?: 993,
            'smtp_host' => $account->smtp_host ?: config('cpanel.mail_host'),
            'smtp_port' => $account->smtp_port ?: 465,
        ];
    }

    private function mailConfigFromData(array $data): array
    {
        return [
            'imap_host' => $data['imap_host'],
            'imap_port' => $data['imap_port'],
            'smtp_host' => $data['smtp_host'],
            'smtp_port' => $data['smtp_port'],
        ];
    }

    private function providerPreset(string $provider): array
    {
        return match ($provider) {
            'gmail' => ['imap_host' => 'imap.gmail.com', 'imap_port' => 993, 'smtp_host' => 'smtp.gmail.com', 'smtp_port' => 465],
            'outlook' => ['imap_host' => 'outlook.office365.com', 'imap_port' => 993, 'smtp_host' => 'smtp.office365.com', 'smtp_port' => 587],
            'cpanel' => ['imap_host' => config('cpanel.mail_host'), 'imap_port' => 993, 'smtp_host' => config('cpanel.mail_host'), 'smtp_port' => 465],
            default => ['imap_host' => '', 'imap_port' => 993, 'smtp_host' => '', 'smtp_port' => 465],
        };
    }
}
