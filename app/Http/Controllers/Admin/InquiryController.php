<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $data = $request->validate(['status' => ['required','in:new,read,in_progress,replied,closed']]);
        $inquiry->update($data);

        return redirect()->route('admin.helpdesk.show', ['type' => 'contact', 'id' => $inquiry->id])
            ->with('status', 'Inquiry status updated.');
    }

    public function show(Inquiry $inquiry): RedirectResponse
    {
        return redirect()->route('admin.helpdesk.show', ['type' => 'contact', 'id' => $inquiry->id]);
    }

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
}
