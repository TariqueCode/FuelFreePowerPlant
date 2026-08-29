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
            $message = $e->getMessage() ?: 'The mail server is temporarily unavailable.';
            if (str_contains($message, 'PHP IMAP extension')) {
                $message = 'Webmail service is not enabled on the hosting server. Please enable the PHP IMAP extension for this site.';
            } elseif (preg_match('/certificate|ssl|tls|verify/i', $message)) {
                $message = 'The mail server TLS certificate could not be verified. Please use the exact IMAP server hostname shown by cPanel Mail Client Setup.';
            } else {
                $message = 'Email or password is incorrect, or the mail server is temporarily unavailable.';
            }
            return back()->withErrors(['email' => $message])->withInput($request->only('email'));
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
            $folders = $webmail->folders($email, $password, $this->mailConfigFor($email));
            $folder = $this->resolveFolder($request, $folders);
            $messages = $webmail->messages($email, $password, 60, $this->mailConfigFor($email), $folder);
        } catch (Throwable $e) {
            report($e);
            $this->clearSession($request);
            return redirect()->to($this->url('/'))->withErrors(['email' => 'Your mailbox connection has expired. Please sign in again.']);
        }

        return view('webmail.inbox', compact('messages', 'email', 'folders', 'folder'));
    }

    public function show(Request $request, int $uid, WebmailService $webmail): View|RedirectResponse
    {
        $credentials = $this->credentials($request);
        if ($credentials === null) return redirect()->to($this->url('/'));
        [$email, $password] = $credentials;
        $folders = $webmail->folders($email, $password, $this->mailConfigFor($email));
        $folder = $this->resolveFolder($request, $folders);

        try {
            $message = $webmail->message($email, $password, $uid, $this->mailConfigFor($email), $folder);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['email' => 'That message could not be opened.']);
        }

        return view('webmail.message', compact('message', 'email', 'folders', 'folder'));
    }

    public function compose(Request $request, WebmailService $webmail): View|RedirectResponse
    {
        $credentials = $this->credentials($request);
        if ($credentials === null) return redirect()->to($this->url('/'));
        [$email, $password] = $credentials;

        $initialTo = '';
        $initialCc = '';
        $initialSubject = '';
        $initialBody = '';
        $mode = 'new';
        $folders = $webmail->folders($email, $password, $this->mailConfigFor($email));
        $folder = $this->resolveFolder($request, $folders);

        if ($request->filled('reply')) {
            $uid = (int) $request->query('reply');
            try {
                $message = $webmail->message($email, $password, $uid, $this->mailConfigFor($email), $folder);
                $initialTo = $this->extractEmail($message['from']);
                $initialCc = '';
                $initialSubject = str_starts_with(strtolower($message['subject']), 're:') ? $message['subject'] : 'Re: '.$message['subject'];
                $initialBody = '<p><br></p><hr><p><strong>Original message</strong><br>'.$this->escapeHtml($message['from']).'<br>'.$this->escapeHtml($message['date']).'</p><blockquote>'.$message['body'].'</blockquote>';
                $mode = 'reply';
            } catch (Throwable $e) {
                report($e);
            }
        } elseif ($request->filled('forward')) {
            $uid = (int) $request->query('forward');
            try {
                $message = $webmail->message($email, $password, $uid, $this->mailConfigFor($email), $folder);
                $initialSubject = str_starts_with(strtolower($message['subject']), 'fwd:') ? $message['subject'] : 'Fwd: '.$message['subject'];
                $initialBody = '<p><br></p><hr><p><strong>Forwarded message</strong><br>From: '.$this->escapeHtml($message['from']).'<br>To: '.$this->escapeHtml($message['to']).'<br>Date: '.$this->escapeHtml($message['date']).'<br>Subject: '.$this->escapeHtml($message['subject']).'</p><blockquote>'.$message['body'].'</blockquote>';
                $mode = 'forward';
            } catch (Throwable $e) {
                report($e);
            }
        }

        return view('webmail.compose', compact('email', 'initialTo', 'initialCc', 'initialSubject', 'initialBody', 'mode', 'folder'));
    }

    public function send(Request $request, WebmailService $webmail): RedirectResponse
    {
        $credentials=$this->credentials($request); if($credentials===null)return redirect()->to($this->url('/'));
        [$email,$password]=$credentials;
        $data=$request->validate([
            'to'=>['required','string','max:5000'],'cc'=>['nullable','string','max:5000'],'bcc'=>['nullable','string','max:5000'],
            'subject'=>['nullable','string','max:255'],'body'=>['required','string','max:500000'],
            'attachments'=>['nullable','array','max:30'],'attachments.*'=>['file','max:102400'],
        ]);
        $attachments=[];
        foreach($request->file('attachments',[]) as $file){if($file&&$file->isValid())$attachments[]=['path'=>$file->getRealPath(),'name'=>$file->getClientOriginalName(),'mime'=>$file->getMimeType()?:'application/octet-stream'];}
        try{
            $webmail->send($email,$password,$data['to'],$data['subject']?:'(No subject)',$data['body'],$this->mailConfigFor($email),$attachments,true,$data['cc']??[],$data['bcc']??[]);
        }catch(Throwable $e){report($e);return back()->withErrors(['send'=>'The message could not be sent: '.($e->getMessage()?:'SMTP error.')])->withInput();}
        return redirect()->to($this->url('/inbox'))->with('status','Message sent successfully.');
    }

    public function attachment(Request $request,int $uid,string $part,WebmailService $webmail)
    {
        $credentials=$this->credentials($request); if($credentials===null)return redirect()->to($this->url('/'));
        [$email,$password]=$credentials; $folder=$this->resolveFolder($request,[]);
        $folder=trim((string)$request->query('folder','INBOX'));
        $data=$webmail->attachment($email,$password,$uid,$part,$this->mailConfigFor($email),$folder);
        return response($data['content'],200,['Content-Type'=>$data['type'],'Content-Disposition'=>'attachment; filename="'.addcslashes($data['name'],'"').'"']);
    }

    public function inline(Request $request,int $uid,string $part,WebmailService $webmail)
    {
        $credentials=$this->credentials($request);
        if($credentials===null)return response('',401);
        [$email,$password]=$credentials;
        $folder=trim((string)$request->query('folder','INBOX'));
        try{
            $data=$webmail->attachment($email,$password,$uid,$part,$this->mailConfigFor($email),$folder);
            $type=strtolower((string)($data['type']??'application/octet-stream'));
            $allowed=['image/jpeg','image/png','image/gif','image/webp','image/bmp'];
            if(!in_array($type,$allowed,true))return response('',404);
            return response($data['content'],200,[
                'Content-Type'=>$type,
                'Content-Disposition'=>'inline',
                'Cache-Control'=>'private, max-age=3600',
                'X-Content-Type-Options'=>'nosniff',
            ]);
        }catch(Throwable $e){
            report($e);
            return response('',404);
        }
    }

    public function delete(Request $request,int $uid,WebmailService $webmail): RedirectResponse
    {
        $credentials=$this->credentials($request); if($credentials===null)return redirect()->to($this->url('/'));
        [$email,$password]=$credentials; $folder=trim((string)$request->input('folder','INBOX'));
        try{$webmail->delete($email,$password,$uid,$folder,$this->mailConfigFor($email));return redirect()->to($this->url('/inbox?folder='.urlencode($folder)))->with('status','Message moved to Trash.');}
        catch(Throwable $e){report($e);return back()->withErrors(['email'=>'The message could not be deleted.']);}
    }

    public function toggleRead(Request $request,int $uid,WebmailService $webmail): RedirectResponse
    {
        $credentials=$this->credentials($request); if($credentials===null)return redirect()->to($this->url('/'));
        [$email,$password]=$credentials;$folder=trim((string)$request->input('folder','INBOX'));$seen=$request->boolean('seen');
        try{$webmail->setSeen($email,$password,$uid,$seen,$this->mailConfigFor($email),$folder);}catch(Throwable $e){report($e);}
        return back();
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->clearSession($request);
        return redirect()->to($this->url('/'));
    }

    private function resolveFolder(Request $request, array $folders): string
    {
        $requested = trim((string) $request->query('folder', 'INBOX'));
        foreach ($folders as $folder) {
            if (($folder['name'] ?? '') === $requested) {
                return $requested;
            }
        }
        return 'INBOX';
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
