<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\HelpDeskReply;
use App\Services\HelpDeskReplyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $inquiries = Inquiry::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();
        return view('admin.inquiries.index', compact('inquiries', 'status'));
    }

    public function show(Inquiry $inquiry): View
    {
        if (!$inquiry->read_at) $inquiry->update(['read_at' => now(), 'status' => $inquiry->status === 'new' ? 'read' : $inquiry->status]);
        $replies = HelpDeskReply::query()->where('inquiry_id', $inquiry->id)->latest('sent_at')->get();
        return view('admin.inquiries.show', compact('inquiry', 'replies'));
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,in_progress,resolved,archived'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);
        $data['resolved_at'] = $data['status'] === 'resolved' ? ($inquiry->resolved_at ?? now()) : null;
        $inquiry->update($data);
        return back()->with('status', 'Inquiry updated successfully.');
    }
    public function reply(Request $request, Inquiry $inquiry, HelpDeskReplyService $replyService): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:50000']]);
        try {
            $replyService->sendInquiry($inquiry, $data['body']);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['reply' => 'Reply could not be sent: '.($e->getMessage() ?: 'mail server error.')])->withInput();
        }
        $inquiry->update(['status' => 'in_progress']);
        return back()->with('status', 'Reply sent successfully.');
    }

}
