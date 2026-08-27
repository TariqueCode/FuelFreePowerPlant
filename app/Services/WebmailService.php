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
        $connection = @imap_open($this->mailbox($config, 'INBOX'), $email, $password, OP_HALFOPEN, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);
        if (!$connection) {
            throw new RuntimeException($this->imapError() ?: 'The email address or password is incorrect.');
        }
        imap_close($connection);
        return true;
    }

    public function folders(string $email, string $password, array $config = []): array
    {
        $this->ensureExtension();
        $connection = @imap_open($this->mailbox($config, ''), $email, $password, OP_HALFOPEN, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);
        if (!$connection) throw new RuntimeException($this->imapError() ?: 'Unable to connect to the mailbox.');

        $list = imap_list($connection, $this->serverPrefix($config), '*') ?: [];
        imap_close($connection);

        $folders = [['name' => 'INBOX', 'label' => 'Inbox', 'icon' => 'fa-inbox']];
        foreach ($list as $raw) {
            $name = $this->decodeFolderName(str_replace($this->serverPrefix($config), '', $raw));
            if (strtoupper($name) === 'INBOX') continue;
            $upper = strtoupper($name);
            if (str_contains($upper, 'SENT')) $folders[] = ['name' => $name, 'label' => 'Sent', 'icon' => 'fa-paper-plane'];
            elseif (str_contains($upper, 'DRAFT')) $folders[] = ['name' => $name, 'label' => 'Drafts', 'icon' => 'fa-file-pen'];
        }

        $unique = [];
        foreach ($folders as $folder) $unique[$folder['label']] = $folder;
        return array_values($unique);
    }

    public function messages(string $email, string $password, int $limit = 40, array $config = [], string $folder = 'INBOX'): array
    {
        $connection = $this->open($email, $password, $config, $folder);
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
                'to' => $this->addressList($header->to ?? []),
                'date' => $header->date ?? '',
                'seen' => empty($header->Unseen),
            ];
        }

        imap_close($connection);
        return $messages;
    }

    public function message(string $email, string $password, int $uid, array $config = [], string $folder = 'INBOX'): array
    {
        $connection = $this->open($email, $password, $config, $folder);
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

    public function send(string $email, string $password, string $to, string $subject, string $body, array $config = [], ?array $attachment = null): void
    {
        $host = $config['smtp_host'] ?? config('cpanel.mail_host', 'mail.fuelfreepowerplant.com');
        $port = (int) ($config['smtp_port'] ?? 465);
        $ssl = $port === 465 ? 'ssl://' : 'tcp://';
        $socket = @stream_socket_client($ssl.$host.':'.$port, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
        if (!$socket) throw new RuntimeException('SMTP connection failed: '.($errstr ?: 'Unable to connect.'));

        stream_set_timeout($socket, 20);
        $this->smtpExpect($socket, 220);
        $ehlo = 'EHLO '.(parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost');
        $this->smtpCommand($socket, $ehlo, 250);
        if ($port === 587) {
            $this->smtpCommand($socket, 'STARTTLS', 220);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($socket);
                throw new RuntimeException('SMTP STARTTLS negotiation failed.');
            }
            $this->smtpCommand($socket, $ehlo, 250);
        }
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
        if ($attachment && !empty($attachment['path']) && is_file($attachment['path'])) {
            $name = $attachment['name'] ?? basename($attachment['path']);
            $mime = $attachment['mime'] ?? 'application/octet-stream';
            $data = chunk_split(base64_encode((string) file_get_contents($attachment['path'])));
            $payload .= '--'.$boundary."\r\nContent-Type: ".$mime."; name=\"".$name."\"\r\nContent-Disposition: attachment; filename=\"".$name."\"\r\nContent-Transfer-Encoding: base64\r\n\r\n".$data."\r\n";
        }
        $payload .= '--'.$boundary."--\r\n.\r\n";

        fwrite($socket, $payload);
        $this->smtpExpect($socket, 250);
        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        // Keep a copy in the provider's Sent folder when IMAP permits it.
        try {
            $sent = $this->findFolder($email, $password, $config, 'SENT');
            if ($sent) {
                $append = implode("\r\n", $headers)."\r\n\r\n".$this->dotStuff($html)."\r\n";
                $connection = $this->open($email, $password, $config, $sent);
                @imap_append($connection, $this->mailbox($config, $sent), $append, '\\Seen');
                imap_close($connection);
            }
        } catch (\Throwable) {
            // Sending succeeded even if the provider does not expose a writable Sent folder.
        }
    }

    private function open(string $email, string $password, array $config = [], string $folder = 'INBOX')
    {
        $this->ensureExtension();
        $connection = @imap_open($this->mailbox($config, $folder), $email, $password, 0, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);
        if (!$connection) throw new RuntimeException($this->imapError() ?: 'Unable to connect to the mailbox.');
        return $connection;
    }

    private function mailbox(array $config, string $folder): string
    {
        return $this->serverPrefix($config).$folder;
    }

    private function serverPrefix(array $config): string
    {
        $host = $config['imap_host'] ?? config('cpanel.mail_host', 'mail.fuelfreepowerplant.com');
        $port = (int) ($config['imap_port'] ?? 993);
        return '{'.$host.':'.$port.'/imap/ssl}';
    }

    private function findFolder(string $email, string $password, array $config, string $needle): ?string
    {
        foreach ($this->folders($email, $password, $config) as $folder) {
            if (strtoupper($needle) === 'SENT' && $folder['label'] === 'Sent') return $folder['name'];
        }
        return null;
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
