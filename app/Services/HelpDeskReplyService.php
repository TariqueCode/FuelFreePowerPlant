<?php

namespace App\Services;

use App\Models\CareerApplication;
use App\Models\EmailAccount;
use App\Models\HelpDeskReply;
use App\Models\Inquiry;
use App\Models\SystemSetting;
use RuntimeException;

class HelpDeskReplyService
{
    public function sendInquiry(Inquiry $inquiry, string $body): HelpDeskReply
    {
        $account = $this->mailbox('mail.contact_account_id', 'info@fuelfreepowerplant.com');
        $subject = str_starts_with(strtolower($inquiry->subject), 're:') ? $inquiry->subject : 'Re: '.$inquiry->subject;

        return $this->send(
            $account,
            $inquiry->email,
            $subject,
            $body,
            ['inquiry_id' => $inquiry->id]
        );
    }

    public function sendCareerApplication(CareerApplication $application, string $body): HelpDeskReply
    {
        $account = $this->mailbox('mail.career_account_id', 'career@fuelfreepowerplant.com');
        $subject = 'Re: '.($application->position ?: 'Career application');

        return $this->send(
            $account,
            $application->email,
            $subject,
            $body,
            ['career_application_id' => $application->id]
        );
    }

    private function mailbox(string $settingKey, string $fallbackAddress): EmailAccount
    {
        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $id = (int) ($settings[$settingKey] ?? 0);

        $account = $id
            ? EmailAccount::query()->whereKey($id)->where('status', 'active')->first()
            : null;

        $account ??= EmailAccount::query()
            ->where('address', $fallbackAddress)
            ->where('status', 'active')
            ->first();

        if (!$account) {
            throw new RuntimeException('No active reply mailbox is configured for this Help Desk conversation.');
        }

        return $account;
    }

    private function send(EmailAccount $account, string $to, string $subject, string $body, array $links): HelpDeskReply
    {
        $reply = HelpDeskReply::create([
            'email_account_id' => $account->id,
            'from_address' => $account->address,
            'to_address' => $to,
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending',
        ] + $links);

        try {
            app(WebmailService::class)->send(
                $account->address,
                $account->password,
                $to,
                $subject,
                nl2br(e($body)),
                [
                    'imap_host' => $account->imap_host,
                    'imap_port' => $account->imap_port,
                    'smtp_host' => $account->smtp_host,
                    'smtp_port' => $account->smtp_port,
                ],
                null,
                false
            );

            $reply->update(['status' => 'sent', 'sent_at' => now()]);
            return $reply;
        } catch (\Throwable $e) {
            $reply->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            throw $e;
        }
    }
}
