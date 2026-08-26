<?php

namespace App\Http\Controllers;

use App\Services\WebmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Throwable;

class WebmailController extends Controller
{
    public function login(): View { return view('webmail.login'); }

    public function authenticate(Request $request, WebmailService $webmail): RedirectResponse
    {
        $data=$request->validate(['email'=>['required','email:rfc,dns','ends_with:@fuelfreepowerplant.com'],'password'=>['required','string','max:500']]);
        try{$webmail->login($data['email'],$data['password']);}catch(Throwable $e){report($e);return back()->withErrors(['email'=>'Email or password is incorrect, or the mail server is temporarily unavailable.'])->withInput($request->only('email'));}
        $request->session()->regenerate();$request->session()->put('webmail.email',strtolower($data['email']));$request->session()->put('webmail.password',Crypt::encryptString($data['password']));
        return redirect()->to($this->url($request,'/inbox'));
    }

    public function inbox(Request $request,WebmailService $webmail): View|RedirectResponse
    {
        $credentials=$this->credentials($request);if($credentials===null)return redirect()->to($this->url($request,'/'));[$email,$password]=$credentials;
        try{$messages=$webmail->messages($email,$password);}catch(Throwable $e){report($e);$this->clearSession($request);return redirect()->to($this->url($request,'/'))->withErrors(['email'=>'Your mailbox connection has expired. Please sign in again.']);}
        return view('webmail.inbox',compact('messages','email'));
    }

    public function show(Request $request,int $uid,WebmailService $webmail): View|RedirectResponse
    {
        $credentials=$this->credentials($request);if($credentials===null)return redirect()->to($this->url($request,'/'));[$email,$password]=$credentials;
        try{$message=$webmail->message($email,$password,$uid);}catch(Throwable $e){report($e);return back()->withErrors(['email'=>'That message could not be opened.']);}
        return view('webmail.message',compact('message','email'));
    }

    public function compose(Request $request): View|RedirectResponse
    {
        $credentials=$this->credentials($request);if($credentials===null)return redirect()->to($this->url($request,'/'));[$email]=$credentials;return view('webmail.compose',compact('email'));
    }

    public function send(Request $request,WebmailService $webmail): RedirectResponse
    {
        $credentials=$this->credentials($request);if($credentials===null)return redirect()->to($this->url($request,'/'));[$email,$password]=$credentials;
        $data=$request->validate(['to'=>['required','email'],'subject'=>['nullable','string','max:255'],'body'=>['required','string','max:500000']]);
        try{$webmail->send($email,$password,$data['to'],$data['subject'] ?: '(No subject)',$data['body']);}catch(Throwable $e){report($e);return back()->withErrors(['send'=>'The message could not be sent. Please try again.'])->withInput();}
        return redirect()->to($this->url($request,'/inbox'))->with('status','Message sent successfully.');
    }

    public function logout(Request $request): RedirectResponse{$this->clearSession($request);return redirect()->to($this->url($request,'/'));}
    private function credentials(Request $request):?array{$email=$request->session()->get('webmail.email');$encrypted=$request->session()->get('webmail.password');if(!$email||!$encrypted)return null;try{return[$email,Crypt::decryptString($encrypted)];}catch(Throwable){$this->clearSession($request);return null;}}
    private function clearSession(Request $request):void{$request->session()->forget(['webmail.email','webmail.password']);$request->session()->regenerateToken();}
    private function url(Request $request,string $path):string{return $request->getHost()==='mail.fuelfreepowerplant.com'?rtrim($request->getSchemeAndHttpHost(),'/').'/'.ltrim($path,'/'):rtrim(config('app.url'),'/').'/webmail'.($path==='/'?'':$path);}
}
