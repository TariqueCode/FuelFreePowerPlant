<?php

namespace App\Services;

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CpanelEmailService
{
    public function configured(): bool
    {
        return (bool) config('cpanel.enabled')
            && filled(config('cpanel.host'))
            && filled(config('cpanel.username'))
            && filled(config('cpanel.token'))
            && filled(config('cpanel.domain'));
    }

    public function create(string $address, string $password): string
    {
        $this->ensureConfigured();
        [$local, $domain] = $this->splitAddress($address);
        $this->ensureDomain($domain);
        return $this->message($this->request('add_pop', [
            'email' => $local,
            'domain' => $domain,
            'password' => $password,
            'quota' => (string) config('cpanel.quota', 0),
        ]), 'Mailbox created in cPanel.');
    }

    public function changePassword(EmailAccount $account, string $password): string
    {
        $this->ensureConfigured();
        [$local, $domain] = $this->splitAddress($account->address);
        $this->ensureDomain($domain);
        return $this->message($this->request('passwd_pop', [
            'email' => $local,
            'domain' => $domain,
            'password' => $password,
        ]), 'Mailbox password changed.');
    }

    public function setActive(EmailAccount $account, bool $active): string
    {
        $this->ensureConfigured();
        [$local, $domain] = $this->splitAddress($account->address);
        $this->ensureDomain($domain);
        $function = $active ? 'unsuspend_login' : 'suspend_login';
        return $this->message($this->request($function, [
            'email' => $local,
            'domain' => $domain,
        ]), $active ? 'Mailbox activated.' : 'Mailbox login suspended.');
    }

    public function delete(EmailAccount $account): string
    {
        if (! $this->configured()) return 'Mailbox record removed. cPanel provisioning is not configured.';
        [$local, $domain] = $this->splitAddress($account->address);
        $this->ensureDomain($domain);
        return $this->message($this->request('delete_pop', ['email' => $local, 'domain' => $domain]), 'Mailbox removed from cPanel.');
    }

    private function ensureConfigured(): void
    {
        if (! $this->configured()) throw new RuntimeException('cPanel mail provisioning is not configured.');
    }

    private function ensureDomain(string $domain): void
    {
        if (strcasecmp($domain, (string) config('cpanel.domain')) !== 0) {
            throw new RuntimeException('Only @'.config('cpanel.domain').' mailboxes are allowed.');
        }
    }

    private function request(string $function, array $query): array
    {
        $base = rtrim((string) config('cpanel.host'), '/');
        if (! str_starts_with($base, 'http://') && ! str_starts_with($base, 'https://')) $base = 'https://'.$base;
        $url = $base.':'.(int) config('cpanel.port', 2083).'/execute/Email/'.$function;
        $response = Http::withHeaders([
            'Authorization' => 'cpanel '.config('cpanel.username').':'.config('cpanel.token'),
            'Accept' => 'application/json',
        ])->timeout(30)->get($url, $query);
        if (! $response->successful()) throw new RuntimeException('cPanel API returned HTTP '.$response->status().'.');
        $payload = $response->json();
        if (! is_array($payload)) throw new RuntimeException('Invalid response received from cPanel.');
        $result = $payload['result'] ?? null;
        if (! is_array($result) || (int) ($result['status'] ?? 0) !== 1) {
            $errors = $result['errors'] ?? null;
            $message = is_array($errors) ? implode(' ', array_map('strval', $errors)) : (string) ($errors ?: 'cPanel rejected the request.');
            throw new RuntimeException($message);
        }
        return $payload;
    }

    private function splitAddress(string $address): array
    {
        $parts = explode('@', $address, 2);
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') throw new RuntimeException('Invalid mailbox address.');
        return [$parts[0], $parts[1]];
    }

    private function message(array $payload, string $fallback): string
    {
        $messages = $payload['result']['messages'] ?? null;
        return is_array($messages) && $messages !== [] ? implode(' ', array_map('strval', $messages)) : $fallback;
    }
}
