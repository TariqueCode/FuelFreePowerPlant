<?php

namespace App\Console\Commands;

use App\Models\EmailAccount;
use App\Models\HelpdeskEmail;
use App\Services\WebmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;

class SyncHelpdeskMail extends Command
{
    protected $signature='fuel-free:sync-helpdesk-mail {--limit=50 : Maximum messages to process per mailbox}';
    protected $description='Import Contact and Career mailbox messages into the Help Desk and purge the external inbox copies.';

    public function handle(WebmailService $webmail): int
    {
        $limit=max(1,(int)$this->option('limit'));
        $accounts=EmailAccount::query()->whereIn('mailbox_group',['contact','career'])->where('status','active')->get();

        foreach($accounts as $account){
            try{
                $messages=$webmail->messages($account->address,$account->password,$limit,$this->mailConfig($account),'INBOX');
            }catch(Throwable $e){
                report($e);
                $this->error($account->address.': mailbox read failed — '.$e->getMessage());
                continue;
            }

            foreach($messages as $summary){
                $uid=(int)($summary['uid']??0);
                if($uid<1) continue;
                $fingerprint=hash('sha256',implode('|',[
                    $account->id,(string)($summary['message_id']??''),(string)$uid,(string)($summary['date']??''),
                    strtolower((string)($summary['from']??'')),(string)($summary['subject']??''),
                ]));

                $existing=HelpdeskEmail::query()->where('fingerprint',$fingerprint)->first();
                if($existing){
                    if(!$existing->external_deleted_at){
                        try{
                                    $webmail->purge($account->address,$account->password,$uid,'INBOX',$this->mailConfig($account));
                            $existing->update(['external_deleted_at'=>now(),'last_error'=>null]);
                        }catch(Throwable $e){
                            $existing->update(['last_error'=>mb_substr($e->getMessage(),0,5000)]);
                        }
                    }
                    continue;
                }

                try{
                    $message=$webmail->message($account->address,$account->password,$uid,$this->mailConfig($account),'INBOX');
                    $received=$this->parseDate($message['date']??$summary['date']??null);
                    $sender=$this->parseSender((string)($message['from']??$summary['from']??''));
                    $bodyHtml=$message['body']??null;
                    $email=HelpdeskEmail::create([
                        'email_account_id'=>$account->id,'mailbox_group'=>$account->mailbox_group,'external_uid'=>$uid,
                        'message_id'=>$message['message_id']??($summary['message_id']??null),'fingerprint'=>$fingerprint,
                        'sender_name'=>$sender['name'],'sender_email'=>$sender['email'] ?: (string)$summary['from'],
                        'to_email'=>$message['to']??null,'cc_email'=>$message['cc']??null,
                        'subject'=>$message['subject']??($summary['subject']??'(No subject)'),
                        'body_html'=>$bodyHtml,'body_text'=>trim(strip_tags((string)($bodyHtml??''))),
                        'status'=>'new','received_at'=>$received,'imported_at'=>now(),
                    ]);

                    foreach(($message['attachments']??[]) as $attachment){
                        $part=(string)($attachment['part']??'');
                        if($part==='') continue;
                        $data=$webmail->attachment($account->address,$account->password,$uid,$part,$this->mailConfig($account),'INBOX');
                        $filename=$this->safeFilename($data['name']??$attachment['name']??'attachment');
                        $path='helpdesk/'.$email->id.'/'.Str::uuid().'-'.$filename;
                        Storage::disk('local')->put($path,$data['content']);
                        $email->attachments()->create([
                            'part'=>$part,'filename'=>$filename,'mime_type'=>$data['type']??($attachment['type']??'application/octet-stream'),
                            'size'=>(int)($data['size']??$attachment['size']??0),'path'=>$path,
                        ]);
                        if(!empty($attachment['inline']) && str_starts_with(strtolower((string)($data['type']??'')),'image/')){
                            $inlineUrl=rtrim(config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com'),'/').'/message/'.$uid.'/inline/'.$part.'?folder='.rawurlencode('INBOX');
                            $bodyHtml=str_ireplace($inlineUrl,'data:'.($data['type']??'application/octet-stream').';base64,'.base64_encode($data['content']),$bodyHtml??'');
                        }
                    }

                    $email->update(['body_html'=>$bodyHtml]);
                    $webmail->purge($account->address,$account->password,$uid,'INBOX',$this->mailConfig($account));
                    $email->update(['external_deleted_at'=>now(),'last_error'=>null]);
                    $this->line($account->address.': imported '.$email->id);
                }catch(Throwable $e){
                    report($e);
                    if(isset($email)){
                        foreach($email->attachments as $stored){
                            if($stored->path) Storage::disk('local')->delete($stored->path);
                        }
                        $email->delete();
                        unset($email);
                    }
                    $this->error($account->address.' UID '.$uid.' import failed — '.$e->getMessage());
                }
            }
        }

        return self::SUCCESS;
    }

    private function mailConfig(EmailAccount $account): array
    {
        return ['imap_host'=>$account->imap_host ?: config('cpanel.mail_host','mail.fuelfreepowerplant.com'),'imap_port'=>$account->imap_port ?: 993,
            'smtp_host'=>$account->smtp_host ?: config('cpanel.mail_host','mail.fuelfreepowerplant.com'),'smtp_port'=>$account->smtp_port ?: 465];
    }

    private function parseDate(?string $value): ?Carbon
    {
        if(!$value) return now();
        try { return Carbon::parse($value); } catch(Throwable) { return now(); }
    }

    private function parseSender(string $value): array
    {
        if(preg_match('/^(.*?)\\s*<([^>]+)>$/',$value,$m)) return ['name'=>trim($m[1],' "\\''),'email'=>trim($m[2])];
        return ['name'=>'','email'=>trim($value)];
    }

    private function safeFilename(string $name): string
    {
        $name=preg_replace('/[^A-Za-z0-9._ -]+/u','_',basename($name)) ?: 'attachment';
        return Str::limit(trim($name),480,'');
    }

}
