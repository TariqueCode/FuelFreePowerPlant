<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerApplication;
use App\Models\EmailAccount;
use App\Models\HelpdeskReply;
use App\Models\Inquiry;
use App\Models\SystemSetting;
use App\Services\WebmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class HelpDeskController extends Controller
{
    public function index(Request $request): View
    {
        $items = collect();

        Inquiry::query()->latest()->get()->each(function ($item) use ($items) {
            $items->push((object) [
                'type' => 'contact',
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'subject' => $item->subject,
                'message' => $item->message,
                'status' => $item->status,
                'received_at' => $item->created_at,
                'route' => route('admin.helpdesk.show', ['type' => 'contact', 'id' => $item->id]),
            ]);
        });

        CareerApplication::query()->latest()->get()->each(function ($item) use ($items) {
            $items->push((object) [
                'type' => 'career',
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'subject' => 'Career application'.($item->position ? ': '.$item->position : ''),
                'message' => $item->message ?: '',
                'status' => $item->status,
                'received_at' => $item->created_at,
                'route' => route('admin.helpdesk.show', ['type' => 'career', 'id' => $item->id]),
            ]);
        });

        $items = $items->sortByDesc('received_at')->values();
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 20;
        $pageItems = $items->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pageItems, $items->count(), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.helpdesk.index', [
            'items' => $paginator,
            'openCount' => $items->whereIn('status', ['new','read','in_progress'])->count(),
            'contactCount' => $items->where('type', 'contact')->count(),
            'careerCount' => $items->where('type', 'career')->count(),
        ]);
    }

    public function show(string $type, int $id): View
    {
        [$source, $label] = $this->source($type, $id);
        $replies = HelpdeskReply::query()
            ->where('source_type', $type)
            ->where('source_id', $id)
            ->with('adminUser')
            ->latest()
            ->get();

        if ($type === 'contact' && !$source->read_at) {
            $source->update(['read_at' => now(), 'status' => $source->status === 'new' ? 'read' : $source->status]);
        }

        return view('admin.helpdesk.show', compact('source', 'label', 'type', 'replies'));
    }

    public function reply(Request $request, string $type, int $id, WebmailService $webmail): RedirectResponse
    {
        [$source] = $this->source($type, $id);

        $data = $request->validate([
            'body' => ['required','string','max:500000'],
        ]);

        $to = trim((string) $source->email);
        $subject = $type === 'contact'
            ? ((str_starts_with(strtolower($source->subject), 're:') ? $source->subject : 'Re: '.$source->subject))
            : 'Re: Career application'.($source->position ? ': '.$source->position : '');

        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $mailboxId = (int) ($settings[$type === 'career' ? 'mail.career_account_id' : 'mail.contact_account_id'] ?? 0);
        $address = $type === 'career' ? 'career@fuelfreepowerplant.com' : 'info@fuelfreepowerplant.com';
        $account = $mailboxId
            ? EmailAccount::query()->whereKey($mailboxId)->where('status', 'active')->first()
            : EmailAccount::query()->where('address', $address)->where('status', 'active')->first();

        if (!$account) {
            return back()->withErrors(['reply' => 'No active official mailbox is configured for this Help Desk channel.']);
        }

        $reply = HelpdeskReply::create([
            'source_type' => $type,
            'source_id' => $id,
            'admin_user_id' => $request->user()->id,
            'to_email' => $to,
            'subject' => $subject,
            'body' => $data['body'],
            'status' => 'pending',
        ]);

        try {
            $webmail->send(
                $account->address,
                $account->password,
                $to,
                $subject,
                $data['body'],
                [
                    'imap_host' => $account->imap_host,
                    'imap_port' => $account->imap_port,
                    'smtp_host' => $account->smtp_host,
                    'smtp_port' => $account->smtp_port,
                ],
                null,
                false
            );

            $reply->update(['status' => 'sent', 'sent_at' => now(), 'error' => null]);

            if ($type === 'contact') {
                $source->update(['status' => 'in_progress']);
            } else {
                $source->update(['status' => 'reviewing']);
            }

            return back()->with('status', 'Reply sent successfully from '.$account->address.'.');
        } catch (Throwable $e) {
            report($e);
            $reply->update(['status' => 'failed', 'error' => mb_substr($e->getMessage(), 0, 5000)]);
            return back()->withErrors(['reply' => 'The reply could not be sent. Check the official mailbox connection and try again.']);
        }
    }

    private function source(string $type, int $id): array
    {
        if ($type === 'contact') {
            return [Inquiry::query()->findOrFail($id), 'Contact inquiry'];
        }

        if ($type === 'career') {
            return [CareerApplication::query()->findOrFail($id), 'Career application'];
        }

        abort(404);
    }
}