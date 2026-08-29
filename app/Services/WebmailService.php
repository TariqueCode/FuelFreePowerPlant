<?php

namespace App\Services;

use App\Models\SystemSetting;
use RuntimeException;

class WebmailService
{
    public function extensionAvailable(): bool
    {
        return function_exists('imap_open');
    }

    public function login(string $email, string $password, array $config = []): bool
    {
        $this->ensureExtension();
        $connection = $this->openConnection($email, $password, $config, 'INBOX', OP_HALFOPEN);
        if (!$connection) {
            throw new RuntimeException($this->imapError() ?: 'The email address or password is incorrect.');
        }
        imap_close($connection);
        return true;
    }

    public function folders(string $email, string $password, array $config = []): array
    {
        $this->ensureExtension();
        $connection = $this->openConnection($email, $password, $config, '', OP_HALFOPEN);
        $prefix = $this->serverPrefix($config);
        $list = imap_getmailboxes($connection, $prefix, '*') ?: [];
        imap_close($connection);

        $found = [['name'=>'INBOX','label'=>'Inbox','icon'=>'fa-inbox','special'=>'inbox']];
        foreach ($list as $mailbox) {
            $raw = (string)($mailbox->name ?? '');
            if ($raw === '') continue;
            $name = $this->decodeFolderName(str_replace($prefix, '', $raw));
            $upper = strtoupper($name);
            if ($upper === 'INBOX') continue;
            $label = null; $icon = 'fa-folder'; $special = 'custom';
            if (preg_match('/(^|[.\\/ ])SENT($|[.\\/ ])/i', $name) || str_contains($upper,'SENT ITEMS')) { $label='Sent'; $icon='fa-paper-plane'; $special='sent'; }
            elseif (preg_match('/DRAFT/i',$name)) { $label='Drafts'; $icon='fa-file-pen'; $special='drafts'; }
            elseif (preg_match('/(SPAM|JUNK)/i',$name)) { $label='Spam'; $icon='fa-shield-halved'; $special='spam'; }
            elseif (preg_match('/(TRASH|DELETED)/i',$name)) { $label='Trash'; $icon='fa-trash'; $special='trash'; }
            elseif (preg_match('/ARCHIVE/i',$name)) { $label='Archive'; $icon='fa-box-archive'; $special='archive'; }
            if ($label) $found[] = compact('name','label','icon','special');
        }
        $priority = ['Inbox'=>0,'Sent'=>1,'Drafts'=>2,'Spam'=>3,'Trash'=>4,'Archive'=>5];
        usort($found, fn($a,$b)=>($priority[$a['label']]??99)<=>($priority[$b['label']]??99));
        $unique=[]; foreach($found as $folder) $unique[$folder['label']]=$folder;
        return array_values($unique);
    }

    public function messages(string $email, string $password, int $limit = 40, array $config = [], string $folder = 'INBOX', string $search = ''): array
    {
        $connection = $this->open($email,$password,$config,$folder);
        $criteria = trim($search) !== '' ? 'TEXT "'.addcslashes(trim($search),'\\\"').'"' : 'ALL';
        $numbers = imap_search($connection,$criteria) ?: [];
        rsort($numbers,SORT_NUMERIC); $numbers=array_slice($numbers,0,$limit); $messages=[];
        foreach($numbers as $number){
            $header=imap_headerinfo($connection,$number);
            $messages[]=[
                'uid'=>(int)imap_uid($connection,$number),'number'=>$number,
                'subject'=>$this->decodeHeader($header->subject??'(No subject)'),
                'from'=>$this->address($header->from[0]??null),'to'=>$this->addressList($header->to??[]),
                'cc'=>$this->addressList($header->cc??[]),'date'=>$header->date??'',
                'seen'=>empty($header->Unseen),'answered'=>!empty($header->Answered),'flagged'=>!empty($header->Flagged),
                'size'=>(int)($header->Size??0),
            ];
        }
        imap_close($connection); return $messages;
    }

    public function message(string $email, string $password, int $uid, array $config = [], string $folder = 'INBOX'): array
    {
        $connection=$this->open($email,$password,$config,$folder);
        $number=imap_msgno($connection,$uid);
        if($number<1){imap_close($connection);throw new RuntimeException('Message not found.');}
        $header=imap_headerinfo($connection,$number); $structure=imap_fetchstructure($connection,$number);
        $parts=['html'=>null,'text'=>null,'attachments'=>[],'inline'=>[]];
        $this->walkParts($connection,$number,$structure,'',$parts);
        if($parts['html']===null && $parts['text']===null){
            $parts['text']=$this->decodeTransfer((string)imap_body($connection,$number,FT_PEEK),(int)($structure->encoding??0));
        }
        imap_setflag_full($connection,(string)$uid,'\\Seen',ST_UID); imap_close($connection);
        return [
            'uid'=>$uid,'subject'=>$this->decodeHeader($header->subject??'(No subject)'),
            'from'=>$this->address($header->from[0]??null),'to'=>$this->addressList($header->to??[]),
            'cc'=>$this->addressList($header->cc??[]),'date'=>$header->date??'',
            'message_id'=>$this->headerValue($header,'message_id'),
            'body'=>$this->replaceInlineCids($this->safeBody($parts['html']??nl2br(e($parts['text']??''))),$parts['inline'],$uid,$folder),
            'attachments'=>$parts['attachments'],
        ];
    }

    public function attachment(string $email,string $password,int $uid,string $part,array $config=[],string $folder='INBOX'): array
    {
        $connection=$this->open($email,$password,$config,$folder); $number=imap_msgno($connection,$uid);
        if($number<1){imap_close($connection);throw new RuntimeException('Message not found.');}
        $structure=imap_fetchstructure($connection,$number); $found=null;
        $this->findPart($connection,$number,$structure,'',$part,$found); imap_close($connection);
        if(!$found)throw new RuntimeException('Attachment not found.'); return $found;
    }

    public function setSeen(string $email,string $password,int $uid,bool $seen,array $config=[],string $folder='INBOX'): void
    {
        $connection=$this->open($email,$password,$config,$folder);
        if($seen)imap_setflag_full($connection,(string)$uid,'\\Seen',ST_UID); else imap_clearflag_full($connection,(string)$uid,'\\Seen',ST_UID);
        imap_close($connection);
    }

    public function move(string $email,string $password,int $uid,string $source,string $destination,array $config=[]): void
    {
        $connection=$this->open($email,$password,$config,$source);
        if(!imap_mail_move($connection,(string)$uid,$this->mailbox($config,$destination),CP_UID)){ $e=$this->imapError()?:'Unable to move message.'; imap_close($connection); throw new RuntimeException($e); }
        imap_expunge($connection); imap_close($connection);
    }

    public function purge(string $email,string $password,int $uid,string $folder,array $config=[]): void
    {
        $connection=$this->open($email,$password,$config,$folder);
        if(!imap_setflag_full($connection,(string)$uid,'\\Deleted',ST_UID)){
            $e=$this->imapError()?:'Unable to permanently delete message.';
            imap_close($connection);
            throw new RuntimeException($e);
        }
        imap_expunge($connection);
        imap_close($connection);
    }

    public function delete(string $email,string $password,int $uid,string $folder,array $config=[]): void
    {
        $trash=collect($this->folders($email,$password,$config))->firstWhere('special','trash');
        if($trash && $trash['name']!==$folder){$this->move($email,$password,$uid,$folder,$trash['name'],$config);return;}
        $connection=$this->open($email,$password,$config,$folder);
        if(!imap_setflag_full($connection,(string)$uid,'\\Deleted',ST_UID)){ $e=$this->imapError()?:'Unable to delete message.'; imap_close($connection); throw new RuntimeException($e); }
        imap_expunge($connection); imap_close($connection);
    }

    public function saveDraft(string $email, string $password, string $to, string $cc, string $bcc, string $subject, string $body, array $config = [], int $existingUid = 0): int
    {
        $draft = collect($this->folders($email, $password, $config))->firstWhere('special', 'drafts');
        if (!$draft) throw new RuntimeException('Drafts folder is not available on this mailbox.');
        $connection = $this->open($email, $password, $config, $draft['name']);

        try {
            if ($existingUid > 0) {
                @imap_setflag_full($connection, (string) $existingUid, '\\Deleted', ST_UID);
                @imap_expunge($connection);
            }

            $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
            $messageId = '<draft-'.bin2hex(random_bytes(18)).'@'.$host.'>';
            $toList = $this->normalizeRecipients($to);
            $ccList = $this->normalizeRecipients($cc);
            $bccList = $this->normalizeRecipients($bcc);
            $html = $this->safeBodyForSend($body);
            $plain = trim(strip_tags(str_replace(['</p>','</div>','<br>','<br/>','<br />'], "\n", $html)));

            $headers = [
                'From: '.$this->formatFromHeader($email),
                'To: '.implode(', ', $toList),
            ];
            if ($ccList) $headers[] = 'Cc: '.implode(', ', $ccList);
            if ($bccList) $headers[] = 'Bcc: '.implode(', ', $bccList);
            $headers[] = 'Subject: =?UTF-8?B?'.base64_encode($subject).'?=';
            $headers[] = 'Date: '.date(DATE_RFC2822);
            $headers[] = 'Message-ID: '.$messageId;
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'X-FuelFree-Draft: 1';
            $headers[] = 'Content-Type: multipart/alternative; boundary="=_FuelFreeDraft_'.bin2hex(random_bytes(10)).'"';

            preg_match('/boundary="([^"]+)"/', end($headers), $boundaryMatch);
            $boundary = $boundaryMatch[1] ?? ('=_FuelFreeDraft_'.bin2hex(random_bytes(10)));
            $headers[count($headers)-1] = 'Content-Type: multipart/alternative; boundary="'.$boundary.'"';

            $payload = implode("\r\n", $headers)."\r\n\r\n";
            $payload .= '--'.$boundary."\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n".quoted_printable_encode($this->dotStuff($plain))."\r\n";
            $payload .= '--'.$boundary."\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n".quoted_printable_encode($this->dotStuff($html))."\r\n";
            $payload .= '--'.$boundary."--\r\n";

            if (!imap_append($connection, $this->mailbox($config, $draft['name']), $payload, '\\Draft \\Seen')) {
                throw new RuntimeException($this->imapError() ?: 'Unable to save the draft.');
            }

            $uids = imap_search($connection, 'HEADER Message-ID "'.addcslashes($messageId, '\\\"').'"', SE_UID) ?: [];
            rsort($uids, SORT_NUMERIC);
            return (int) ($uids[0] ?? 0);
        } finally {
            imap_close($connection);
        }
    }

    public function deleteDraft(string $email, string $password, int $uid, array $config = []): void
    {
        $draft = collect($this->folders($email, $password, $config))->firstWhere('special', 'drafts');
        if (!$draft) return;
        $connection = $this->open($email, $password, $config, $draft['name']);
        try {
            @imap_setflag_full($connection, (string) $uid, '\\Deleted', ST_UID);
            @imap_expunge($connection);
        } finally {
            imap_close($connection);
        }
    }

    public function send(string $email,string $password,string|array $to,string $subject,string $body,array $config=[],?array $attachments=null,bool $saveSent=false,string|array $cc=[],string|array $bcc=[],array $headersExtra=[]): void
    {
        $to=$this->normalizeRecipients($to); $cc=$this->normalizeRecipients($cc); $bcc=$this->normalizeRecipients($bcc);
        if(!$to)throw new RuntimeException('At least one recipient is required.');
        $host=$config['smtp_host']??config('cpanel.mail_host','mail.fuelfreepowerplant.com'); $port=(int)($config['smtp_port']??465);
        $socket=@stream_socket_client(($port===465?'ssl://':'tcp://').$host.':'.$port,$errno,$errstr,20,STREAM_CLIENT_CONNECT);
        if(!$socket)throw new RuntimeException('SMTP connection failed: '.($errstr?:'Unable to connect.'));
        stream_set_timeout($socket,20);
        try{
            $this->smtpExpect($socket,220); $ehlo='EHLO '.(parse_url(config('app.url'),PHP_URL_HOST)?:'localhost'); $this->smtpCommand($socket,$ehlo,250);
            if($port===587){$this->smtpCommand($socket,'STARTTLS',220);if(!stream_socket_enable_crypto($socket,true,STREAM_CRYPTO_METHOD_TLS_CLIENT))throw new RuntimeException('SMTP STARTTLS negotiation failed.');$this->smtpCommand($socket,$ehlo,250);}
            $this->smtpCommand($socket,'AUTH LOGIN',334);$this->smtpCommand($socket,base64_encode($email),334);$this->smtpCommand($socket,base64_encode($password),235);
            $this->smtpCommand($socket,'MAIL FROM:<'.$email.'>',250); foreach(array_merge($to,$cc,$bcc) as $recipient)$this->smtpCommand($socket,'RCPT TO:<'.$recipient.'>',250); $this->smtpCommand($socket,'DATA',354);
            $html=$this->brandedEmailTemplate($this->safeBodyForSend($body),$email,$subject); $plain=trim(strip_tags(str_replace(['</p>','</div>','<br>','<br/>','<br />'],"\n",$this->safeBodyForSend($body))));
            $hasAttachments=false; foreach(($attachments??[]) as $a)if(!empty($a['path'])&&is_file($a['path'])){$hasAttachments=true;break;}
            $boundary='=_FuelFreePowerPlant_'.bin2hex(random_bytes(12)); $headers=['From: '.$this->formatFromHeader($email),'To: '.implode(', ',$to)];
            if($cc)$headers[]='Cc: '.implode(', ',$cc); $headers[]='Subject: =?UTF-8?B?'.base64_encode($subject).'?='; $headers[]='Date: '.date(DATE_RFC2822);
            $headers[]='Message-ID: <'.bin2hex(random_bytes(16)).'@'.(parse_url(config('app.url'),PHP_URL_HOST)?:'localhost'); foreach($headersExtra as $k=>$v)if($v!=='')$headers[]=$k.': '.$v;
            $headers[]='MIME-Version: 1.0';
            if(!$hasAttachments){
                $headers[]='Content-Type: multipart/alternative; boundary="'.$boundary.'"'; $payload=implode("\r\n",$headers)."\r\n\r\n";
                $payload.='--'.$boundary."\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n".quoted_printable_encode($this->dotStuff($plain))."\r\n";
                $payload.='--'.$boundary."\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n".quoted_printable_encode($this->dotStuff($html))."\r\n--".$boundary."--\r\n.\r\n";
            }else{
                $alt='=_Alt_'.bin2hex(random_bytes(8)); $headers[]='Content-Type: multipart/mixed; boundary="'.$boundary.'"'; $payload=implode("\r\n",$headers)."\r\n\r\n";
                $payload.='--'.$boundary."\r\nContent-Type: multipart/alternative; boundary=\"".$alt."\"\r\n\r\n";
                $payload.='--'.$alt."\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n".quoted_printable_encode($this->dotStuff($plain))."\r\n";
                $payload.='--'.$alt."\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n".quoted_printable_encode($this->dotStuff($html))."\r\n--".$alt."--\r\n";
                foreach($attachments??[] as $a){if(empty($a['path'])||!is_file($a['path']))continue;$name=$this->headerFilename($a['name']??basename($a['path']));$mime=$a['mime']??'application/octet-stream';$data=chunk_split(base64_encode((string)file_get_contents($a['path'])));$payload.='--'.$boundary."\r\nContent-Type: {$mime}; name=\"{$name}\"\r\nContent-Disposition: attachment; filename=\"{$name}\"\r\nContent-Transfer-Encoding: base64\r\n\r\n{$data}\r\n";}
                $payload.='--'.$boundary."--\r\n.\r\n";
            }
            fwrite($socket,$payload);$this->smtpExpect($socket,250);fwrite($socket,"QUIT\r\n");
        }finally{fclose($socket);}
        if(!$saveSent)return;
        try{$sent=$this->findFolder($email,$password,$config,'SENT');if($sent){$connection=$this->open($email,$password,$config,$sent);@imap_append($connection,$this->mailbox($config,$sent),$payload,'\\Seen');imap_close($connection);}}catch (\Throwable $e){}
    }

    private function walkParts($connection,int $number,?object $part,string $partNo,array &$parts): void
    {
        if(!$part)return; $type=$this->mimeType((int)($part->type??0)); $sub=strtolower((string)($part->subtype??'')); $filename=$this->partFilename($part); $disposition=strtolower((string)($part->disposition??''));
        $cid=trim((string)($part->id ?? $part->ifid ?? ''),"<> \t\r\n");
        if($type==='image'&&$cid!==''){$parts['inline'][]=['part'=>$partNo?:'1','type'=>$type.'/'.$sub,'cid'=>$cid];}
        if($filename!==''||$disposition==='attachment'||($type==='image'&&$cid!=='')){$parts['attachments'][]=['part'=>$partNo?:'1','name'=>$filename?:'inline-image','type'=>$type.'/'.$sub,'size'=>(int)($part->bytes??0),'inline'=>($disposition==='inline'||($type==='image'&&$cid!=='')),'cid'=>$cid];}
        elseif($type==='text'&&in_array($sub,['html','plain'],true)){ $raw=$partNo!==''?imap_fetchbody($connection,$number,$partNo,FT_PEEK):imap_body($connection,$number,FT_PEEK); $decoded=$this->convertCharset($this->decodeTransfer((string)$raw,(int)($part->encoding??0)),$this->partCharset($part)); if($sub==='html')$parts['html']=$decoded;elseif($parts['text']===null)$parts['text']=$decoded; }
        foreach(($part->parts??[]) as $i=>$child){$childNo=$partNo!==''?$partNo.'.'.($i+1):(string)($i+1);$this->walkParts($connection,$number,$child,$childNo,$parts);}
    }

    private function findPart($connection,int $number,?object $part,string $partNo,string $wanted,?array &$found): void
    {
        if(!$part||$found)return; if($partNo===$wanted){$raw=$partNo!==''?imap_fetchbody($connection,$number,$partNo,FT_PEEK):imap_body($connection,$number,FT_PEEK);$found=['name'=>$this->partFilename($part)?:'attachment','type'=>$this->mimeType((int)($part->type??3)).'/'.strtolower((string)($part->subtype??'octet-stream')),'content'=>$this->decodeTransfer((string)$raw,(int)($part->encoding??0)),'size'=>(int)($part->bytes??0)];return;} foreach(($part->parts??[]) as $i=>$child){$childNo=$partNo!==''?$partNo.'.'.($i+1):(string)($i+1);$this->findPart($connection,$number,$child,$childNo,$wanted,$found);}
    }

    private function replaceInlineCids(string $html,array $inline,int $uid,string $folder): string
    {
        $base=rtrim(config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com'),'/');
        foreach($inline as $item){
            $cid=trim((string)($item['cid']??''),"<> \t\r\n");
            $part=(string)($item['part']??'');
            if($cid===''||$part==='')continue;
            $url=$base.'/message/'.$uid.'/inline/'.$part.'?folder='.rawurlencode($folder);
            $html=str_ireplace(['cid:'.$cid,'cid:'.rawurlencode($cid)],$url,$html);
        }
        return $html;
    }

    private function decodeTransfer(string $body,int $encoding): string{return match($encoding){3=>(base64_decode($body,true)?:''),4=>quoted_printable_decode($body),default=>$body};}
    private function convertCharset(string $value,string $charset): string{$charset=strtoupper(trim($charset));if($charset===''||$charset==='UTF-8'||$charset==='US-ASCII')return $value;return function_exists('mb_convert_encoding')?(mb_convert_encoding($value,'UTF-8',$charset)?:$value):$value;}
    private function partFilename(object $part): string{foreach(['dparameters','parameters'] as $key)foreach(($part->{$key}??[]) as $param){$attr=strtolower((string)($param->attribute??''));if(in_array($attr,['filename','name'],true))return $this->decodeHeader((string)($param->value??''));}return '';}
    private function partCharset(object $part): string{foreach(($part->parameters??[]) as $param)if(strtolower((string)($param->attribute??''))==='charset')return (string)$param->value;return '';}
    private function mimeType(int $type): string{return ['text','multipart','message','application','audio','image','video','other'][$type]??'application';}

    private function open(string $email, string $password, array $config = [], string $folder = 'INBOX')
    {
        $this->ensureExtension();
        $connection = $this->openConnection($email, $password, $config, $folder, 0);
        if (!$connection) throw new RuntimeException($this->imapError() ?: 'Unable to connect to the mailbox.');
        return $connection;
    }

    private function openConnection(string $email, string $password, array $config, string $folder, int $flags)
    {
        $errors = [];
        foreach ($this->mailboxCandidates($config, $folder) as $mailbox) {
            $connection = @imap_open($mailbox, $email, $password, $flags, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);
            if ($connection) return $connection;
            $errors[] = $this->imapError();
        }

        $message = trim((string) end($errors));
        if ($message === '') $message = 'The email address or password is incorrect, or the IMAP server rejected the connection.';
        throw new RuntimeException($message);
    }

    private function mailboxCandidates(array $config, string $folder): array
    {
        $host = $config['imap_host'] ?? config('cpanel.mail_host', 'mail.fuelfreepowerplant.com');
        $port = (int) ($config['imap_port'] ?? 993);
        if ($port === 993) {
            return [
                '{'.$host.':'.$port.'/imap/ssl}'.$folder,
                '{'.$host.':'.$port.'/imap/ssl/novalidate-cert}'.$folder,
            ];
        }
        return ['{'.$host.':'.$port.'/imap}'.$folder];
    }

    private function mailbox(array $config, string $folder): string
    {
        return $this->serverPrefix($config).$folder;
    }

    private function serverPrefix(array $config): string
    {
        $host = $config['imap_host'] ?? config('cpanel.mail_host', 'mail.fuelfreepowerplant.com');
        $port = (int) ($config['imap_port'] ?? 993);
        return $port === 993 ? '{'.$host.':'.$port.'/imap/ssl}' : '{'.$host.':'.$port.'/imap}';
    }

    private function findFolder(string $email, string $password, array $config, string $needle): ?string
    {
        foreach ($this->folders($email, $password, $config) as $folder) {
            $wanted = strtoupper($needle);
            if ($wanted === 'SENT' && $folder['label'] === 'Sent') return $folder['name'];
            if ($wanted === 'DRAFTS' && $folder['label'] === 'Drafts') return $folder['name'];
        }
        return null;
    }

    private function looksLikeCertificateError(): bool
    {
        $error = strtolower($this->imapError());
        return str_contains($error, 'certificate')
            || str_contains($error, 'ssl')
            || str_contains($error, 'tls')
            || str_contains($error, 'verify');
    }

    private function ensureExtension(): void
    {
        if (!$this->extensionAvailable()) throw new RuntimeException('PHP IMAP extension is not enabled on this server. Enable IMAP in PHP extensions.');
    }

    private function imapError(): string
    {
        return function_exists('imap_last_error') ? (string) imap_last_error() : '';
    }

    private function decodeHeader(string $value): string
    {
        return function_exists('imap_utf8') ? imap_utf8($value) : $value;
    }

    private function decodeFolderName(string $value): string
    {
        return function_exists('imap_utf8') ? imap_utf8($value) : $value;
    }

    private function normalizeRecipients(string|array $value): array{$items=is_array($value)?$value:(preg_split('/[,;]+/',str_replace(["\r","\n"],' ',$value))?:[]);$out=[];foreach($items as $item){$item=trim($item);if($item!==''&&filter_var($item,FILTER_VALIDATE_EMAIL))$out[]=strtolower($item);}return array_values(array_unique($out));}
    private function headerValue(object $header,string $property): string{return isset($header->{$property})?(string)$header->{$property}:'';}
    private function headerFilename(string $name): string{return str_replace(["\r","\n",'"'],['','',''],basename($name));}

    private function address(?object $address): string
    {
        if (!$address) return '';
        $name = trim($this->decodeHeader((string) ($address->personal ?? '')));
        $mail = ($address->mail ?? '').'@'.($address->host ?? '');
        return $name !== '' ? $name.' <'.$mail.'>' : $mail;
    }

    private function addressList(array $addresses): string
    {
        return implode(', ', array_map(fn ($address) => $this->address($address), $addresses));
    }

    private function decodeBody(string $body, ?object $structure): string
    {
        if (!$structure) return $body;
        if (($structure->encoding ?? 0) === 3) return base64_decode($body) ?: '';
        if (($structure->encoding ?? 0) === 4) return quoted_printable_decode($body);
        return $body;
    }

    private function safeBody(string $body): string
    {
        $body = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $body) ?? $body;
        $body = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $body) ?? $body;
        $body = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $body) ?? $body;
        $body = preg_replace('/\s+(?:href|src)\s*=\s*(?:"|\')\s*javascript:[^"\']*(?:"|\')/i', '', $body) ?? $body;
        $allowed = '<p><br><div><span><strong><b><em><i><u><s><blockquote><ul><ol><li><a><img><table><thead><tbody><tfoot><tr><th><td><h1><h2><h3><h4><hr><pre><code>';
        return $body !== strip_tags($body) ? strip_tags($body, $allowed) : nl2br(e($body));
    }

    private function safeBodyForSend(string $body): string
    {
        return $this->safeBody($body);
    }

    private function formatFromHeader(string $email): string
    {
        $email = strtolower(trim($email));
        $name = str_starts_with($email, 'career@')
            ? 'FuelFree PowerPlant | Careers'
            : 'FuelFree PowerPlant | Contact';

        return '=?UTF-8?B?'.base64_encode($name).'?= <'.$email.'>';
    }

    private function brandedEmailTemplate(string $body, string $email, string $subject): string
    {
        $email = strtolower(trim($email));
        $isCareer = str_starts_with($email, 'career@');
        $channel = $isCareer ? 'Careers' : 'Contact';
        $accent = '#51D8F0';
        $dark = '#020A10';
        $panel = '#061721';
        $text = '#EFFCFF';
        $muted = '#8AA8B1';
        $website = rtrim((string) config('app.url', 'https://www.fuelfreepowerplant.com'), '/');
        if ($website === '') $website = 'https://www.fuelfreepowerplant.com';
        $logoPath = (string) (SystemSetting::query()->where('key','company.logo_path')->value('value') ?? '');
        $logoUrl = $logoPath !== '' ? $website.'/storage/'.ltrim($logoPath,'/') : '';

        $logoMarkup = $logoUrl !== ''
            ? '<img src="'.$logoUrl.'" alt="FuelFree PowerPlant" width="42" height="42" style="display:block;width:42px;height:42px;object-fit:contain;border-radius:8px;">'
            : '<div style="width:42px;height:42px;line-height:42px;text-align:center;border:1px solid #24515f;border-radius:8px;color:'.$accent.';font-size:20px;font-weight:800;">F</div>';

        return '<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>'
            .e($subject)
            .'</title></head><body style="margin:0;padding:0;background:#edf3f5;font-family:Arial,Helvetica,sans-serif;color:'.$text.';">'
            .'<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#edf3f5;padding:24px 12px;">'
            .'<tr><td align="center"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:680px;background:'.$dark.';border:1px solid #17313b;border-radius:16px;overflow:hidden;">'
            .'<tr><td style="height:4px;background:'.$accent.';font-size:0;line-height:0;">&nbsp;</td></tr>'
            .'<tr><td style="padding:24px 28px 20px;background:#031018;border-bottom:1px solid #17313b;">'
            .'<table role="presentation" cellspacing="0" cellpadding="0" border="0"><tr><td style="padding-right:12px;vertical-align:middle;">'.$logoMarkup.'</td><td style="vertical-align:middle;"><div style="font-size:20px;font-weight:800;line-height:1.2;color:'.$text.';"><span style="color:'.$accent.';">FUELFREE</span> POWERPLANT</div></td></tr></table>'
            .'<div style="margin-top:7px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:'.$accent.';">'.$channel.' communication</div>'
            .'</td></tr>'
            .'<tr><td style="padding:28px;background:'.$panel.';font-size:15px;line-height:1.75;color:#C4D5DA;">'
            .$body
            .'</td></tr>'
            .'<tr><td style="padding:22px 28px;background:#031018;border-top:1px solid #17313b;">'
            .'<div style="font-size:14px;font-weight:800;color:'.$text.';"><span style="color:'.$accent.';">FuelFree</span> PowerPlant</div>'
            .'<div style="margin-top:5px;font-size:11px;line-height:1.6;color:'.$muted.';">Powering a cleaner, smarter future.</div>'
            .'<div style="margin-top:12px;font-size:11px;line-height:1.7;color:'.$muted.';">'
            .'House-141, 3rd Floor, Road-22, Mohakhali DOHS, Dhaka-1206, Bangladesh<br>'
            .'<a href="mailto:'.$email.'" style="color:'.$accent.';text-decoration:none;">'.$email.'</a>'
            .' &nbsp;·&nbsp; <a href="'.$website.'" style="color:'.$accent.';text-decoration:none;">www.fuelfreepowerplant.com</a>'
            .'</div>'
            .'<div style="margin-top:14px;padding-top:12px;border-top:1px solid #17313b;font-size:10px;color:#607E89;">© '.date('Y').' FuelFree PowerPlant · All rights reserved.</div>'
            .'</td></tr></table></td></tr></table></body></html>';
    }

    private function smtpCommand($socket, string $command, int $expected): string
    {
        fwrite($socket, $command."\r\n");
        return $this->smtpExpect($socket, $expected);
    }

    private function smtpExpect($socket, int $expected): string
    {
        $response = '';
        while (($line = fgets($socket, 2048)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        $code = (int) substr($response, 0, 3);
        if ($code !== $expected) throw new RuntimeException('SMTP error: '.trim($response));
        return $response;
    }

    private function dotStuff(string $body): string
    {
        return preg_replace('/^\./m', '..', str_replace(["\r\n", "\r"], "\n", $body)) ?? $body;
    }
}
