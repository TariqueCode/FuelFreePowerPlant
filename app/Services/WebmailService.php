<?php

namespace App\Services;

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
        $connection = @imap_open($this->mailbox($config), $email, $password, OP_HALFOPEN, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);
        if (!$connection) throw new RuntimeException($this->imapError() ?: 'The email address or password is incorrect.');
        imap_close($connection);
        return true;
    }

    public function messages(string $email, string $password, int $limit = 40, array $config = []): array
    {
        $connection = $this->open($email, $password, $config);
        $numbers = imap_search($connection, 'ALL') ?: [];
        rsort($numbers);
        $numbers = array_slice($numbers, 0, $limit);
        $messages = [];

        foreach ($numbers as $number) {
            $header = imap_headerinfo($connection, $number);
            $messages[] = [
                'uid' => imap_uid($connection, $number),
                'number' => $number,
                'subject' => $this->decodeHeader($header->subject ?? '(No subject)'),
                'from' => $this->address($header->from[0] ?? null),
                'date' => $header->date ?? '',
                'seen' => empty($header->Unseen),
            ];
        }

        imap_close($connection);
        return $messages;
    }

    public function message(string $email, string $password, int $uid, array $config = []): array
    {
        $connection = $this->open($email, $password, $config);
        $number = imap_msgno($connection, $uid);
        if ($number < 1) {
            imap_close($connection);
            throw new RuntimeException('Message not found.');
        }

        $header = imap_headerinfo($connection, $number);
        $body = imap_fetchbody($connection, $number, '1', FT_PEEK);
        if ($body === '' || $body === false) $body = imap_body($connection, $number, FT_PEEK);
        $structure = imap_fetchstructure($connection, $number);
        $body = $this->decodeBody((string) $body, $structure);
        imap_setflag_full($connection, (string) $number, '\\Seen');
        imap_close($connection);

        return [
            'uid' => $uid,
            'subject' => $this->decodeHeader($header->subject ?? '(No subject)'),
            'from' => $this->address($header->from[0] ?? null),
            'to' => $this->addressList($header->to ?? []),
            'date' => $header->date ?? '',
            'body' => $this->safeBody($body),
        ];
    }

    public function send(string $email, string $password, string $to, string $subject, string $body, array $config = []): void
    {
        $host = $config['smtp_host'] ?? config('cpanel.mail_host', 'mail.fuelfreepowerplant.com');
        $socket = @stream_socket_client('ssl://'.$host.':'.($config['smtp_port'] ?? 465), $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
        if (!$socket) throw new RuntimeException('SMTP connection failed: '.($errstr ?: 'Unable to connect.'));

        stream_set_timeout($socket, 20);
        $this->smtpExpect($socket, 220);
        $this->smtpCommand($socket, 'EHLO '.(parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost'), 250);
        $this->smtpCommand($socket, 'AUTH LOGIN', 334);
        $this->smtpCommand($socket, base64_encode($email), 334);
        $this->smtpCommand($socket, base64_encode($password), 235);
        $this->smtpCommand($socket, 'MAIL FROM:<'.$email.'>', 250);
        $this->smtpCommand($socket, 'RCPT TO:<'.$to.'>', 250);
        $this->smtpCommand($socket, 'DATA', 354);

        $html = $this->safeBodyForSend($body);
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags(str_replace(['</p>', '</div>', '<br>', '<br/>', '<br />'], "\n", $html))) ?? $body);
        $boundary = '=_FuelFreePowerPlant_'.bin2hex(random_bytes(12));
        $headers = [
            'From: '.$email,
            'To: '.$to,
            'Subject: =?UTF-8?B?'.base64_encode($subject).'?=',
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="'.$boundary.'"',
            'Date: '.date(DATE_RFC2822),
        ];

        $payload = implode("\r\n", $headers)."\r\n\r\n";
        $payload .= '--'.$boundary."\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n".$this->dotStuff($plain)."\r\n";
        $payload .= '--'.$boundary."\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n".$this->dotStuff($html)."\r\n";
        $payload .= '--'.$boundary."--\r\n.\r\n";

        fwrite($socket, $payload);
        $this->smtpExpect($socket, 250);
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
    }

    private function open(string $email, string $password, array $config = [])
    {
        $this->ensureExtension();
        $connection = @imap_open($this->mailbox($config), $email, $password, 0, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);
        if (!$connection) throw new RuntimeException($this->imapError() ?: 'Unable to connect to the mailbox.');
        return $connection;
    }

    private function mailbox(array $config = []): string
    {
        $host = $config['imap_host'] ?? config('cpanel.mail_host', 'mail.fuelfreepowerplant.com');
        $port = $config['imap_port'] ?? 993;
        return '{'.$host.':'.$port.'/imap/ssl}INBOX';
    }

    private function ensureExtension(): void
    {
        if (!$this->extensionAvailable()) throw new RuntimeException('PHP IMAP extension is not enabled on this server. Please enable IMAP in the hosting PHP extensions.');
    }

    private function imapError(): string
    {
        return function_exists('imap_last_error') ? (string) imap_last_error() : '';
    }

    private function decodeHeader(string $value): string
    {
        return function_exists('imap_utf8') ? imap_utf8($value) : $value;
    }

    private function address(?object $address): string
    {
        if (!$address) return '';
        $name = trim($this->decodeHeader((string) ($address->personal ?? '')));
        $mail = ($address->mail ?? '').'@'.($address->host ?? '');
        return $name !== '' ? $name.' <'.$mail.'>' : $mail;
    }

    private function addressList(array $addresses): string
    {
        return implode(', ', array_map(fn($address) => $this->address($address), $addresses));
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
        if ($body !== strip_tags($body)) return strip_tags($body, $allowed);
        return nl2br(e($body));
    }

    private function safeBodyForSend(string $body): string
    {
        return $this->safeBody($body);
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
