<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use App\Services\WebmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Throwable;

class WebmailController extends Controller
{
    public function login(): View
    {
        return view('webmail.login');
    }

    public function authenticate(Request $request, WebmailService $webmail): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required','email:rfc,dns','ends_with:@fuelfreepowerplant.com'],
            'password' => ['required','string','max:500'],
        ]);

        try {
            $webmail->login($data['email'], $data['password'], $this->mailConfigFor($data['email']));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['email' => 'Email or password is incorrect, or the mail server is temporarily unavailable.'])->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        $request->session()->put('webmail.email', strtolower($data['email']));
        $request->session()->put('webmail.password', Crypt::encryptString($data['password']));

        return redirect()->to($this->url('/inbox'));
    }

    public function inbox(Request $request, WebmailService $webmail): View|RedirectResponse
    {
        $credentials = $this->credentials($request);
        if ($credentials === null) return redirect()->to($this->url('/'));
        [$email, $password] = $credentials;

        try {
            $messages = $webmail->messages($email, $password, 40, $this->mailConfigFor($email));
        } catch (Throwable $e) {
            report($e);
            $this->clearSession($request);
            return redirect()->to($this->url('/'))->withErrors(['email' => 'Your mailbox connection has expired. Please sign in again.']);
        }

        return view('webmail.inbox', compact('messages', 'email'));
    }

    public function show(Request $request, int $uid, WebmailService $webmail): View|RedirectResponse
    {
        $credentials = $this->credentials($request);
        if ($credentials === null) return redirect()->to($this->url('/'));
        [$email, $password] = $credentials;

        try {
            $message = $webmail->message($email, $password, $uid, $this->mailConfigFor($email));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['email' => 'That message could not be opened.']);
        }

        return view('webmail.message', compact('message', 'email'));
    }

    public function compose(Request $request, WebmailService $webmail): View|RedirectResponse
    {
        $credentials = $this->credentials($request);
        if ($credentials === null) return redirect()->to($this->url('/'));
        [$email, $password] = $credentials;

        $initialTo = '';
        $initialSubject = '';
        $initialBody = '';
        $mode = 'new';

        if ($request->filled('reply')) {
            $uid = (int) $request->query('reply');
            try {
                $message = $webmail->message($email, $password, $uid);
                $initialTo = $this->extractEmail($message['from']);
                $initialSubject = str_starts_with(strtolower($message['subject']), 're:') ? $message['subject'] : 'Re: '.$message['subject'];
                $initialBody = '<p><br></p><hr><p><strong>Original message</strong><br>'.$this->escapeHtml($message['from']).'<br>'.$this->escapeHtml($message['date']).'</p><blockquote>'.$message['body'].'</blockquote>';
                $mode = 'reply';
            } catch (Throwable $e) {
                report($e);
            }
        } elseif ($request->filled('forward')) {
            $uid = (int) $request->query('forward');
            try {
                $message = $webmail->message($email, $password, $uid);
                $initialSubject = str_starts_with(strtolower($message['subject']), 'fwd:') ? $message['subject'] : 'Fwd: '.$message['subject'];
                $initialBody = '<p><br></p><hr><p><strong>Forwarded message</strong><br>From: '.$this->escapeHtml($message['from']).'<br>To: '.$this->escapeHtml($message['to']).'<br>Date: '.$this->escapeHtml($message['date']).'<br>Subject: '.$this->escapeHtml($message['subject']).'</p><blockquote>'.$message['body'].'</blockquote>';
                $mode = 'forward';
            } catch (Throwable $e) {
                report($e);
            }
        }

        return view('webmail.compose', compact('email', 'initialTo', 'initialSubject', 'initialBody', 'mode'));
    }

    public function send(Request $request, WebmailService $webmail): RedirectResponse
    {
        $credentials = $this->credentials($request);
        if ($credentials === null) return redirect()->to($this->url('/'));
        [$email, $password] = $credentials;

        $data = $request->validate([
            'to' => ['required','email'],
            'subject' => ['nullable','string','max:255'],
            'body' => ['required','string','max:500000'],
        ]);

        try {
            $webmail->send($email, $password, $data['to'], $data['subject'] ?: '(No subject)', $data['body'], $this->mailConfigFor($email));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['send' => 'The message could not be sent. Please try again.'])->withInput();
        }

        return redirect()->to($this->url('/inbox'))->with('status', 'Message sent successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->clearSession($request);
        return redirect()->to($this->url('/'));
    }

    private function mailConfigFor(string $email): array
    {
        $account = EmailAccount::query()
            ->where('address', strtolower(trim($email)))
            ->where('status', 'active')
            ->first(['imap_host','imap_port','smtp_host','smtp_port']);

        return [
            'imap_host' => $account?->imap_host ?: config('cpanel.mail_host', 'mail.fuelfreepowerplant.com'),
            'imap_port' => $account?->imap_port ?: 993,
            'smtp_host' => $account?->smtp_host ?: config('cpanel.mail_host', 'mail.fuelfreepowerplant.com'),
            'smtp_port' => $account?->smtp_port ?: 465,
        ];
    }

    private function credentials(Request $request): ?array
    {
        $email = $request->session()->get('webmail.email');
        $encrypted = $request->session()->get('webmail.password');
        if (!$email || !$encrypted) return null;
        try {
            return [$email, Crypt::decryptString($encrypted)];
        } catch (Throwable) {
            $this->clearSession($request);
            return null;
        }
    }

    private function clearSession(Request $request): void
    {
        $request->session()->forget(['webmail.email', 'webmail.password']);
        $request->session()->regenerateToken();
    }

    private function url(string $path): string
    {
        return rtrim(config('cpanel.webmail_url', 'https://mail.fuelfreepowerplant.com'), '/').'/'.ltrim($path, '/');
    }

    private function extractEmail(string $value): string
    {
        if (preg_match('/<([^>]+)>/', $value, $match)) return trim($match[1]);
        return trim($value);
    }

    private function escapeHtml(string $value): string
    {
        return e($value);
    }
}
