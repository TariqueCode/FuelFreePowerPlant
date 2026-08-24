<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminModuleController extends Controller
{
    public function email(): View
    {
        return view('admin.modules.email');
    }

    public function support(Request $request): View
    {
        $user = $request->user();
        $isStaff = $user->hasRole(['super-admin', 'administrator', 'project-manager', 'support-agent']);
        $tickets = SupportTicket::query()
            ->with('user')
            ->withCount('messages')
            ->when(!$isStaff, fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(15);

        $openCount = (clone $tickets->getCollection()->isNotEmpty() ? SupportTicket::query() : SupportTicket::query())
            ->when(!$isStaff, fn ($query) => $query->where('user_id', $user->id))
            ->whereIn('status', ['open', 'in-progress'])
            ->count();
        $priorityCount = (clone $tickets->getCollection()->isNotEmpty() ? SupportTicket::query() : SupportTicket::query())
            ->when(!$isStaff, fn ($query) => $query->where('user_id', $user->id))
            ->where('priority', 'high')
            ->whereNotIn('status', ['closed'])
            ->count();

        return view('admin.modules.support', compact('tickets', 'openCount', 'priorityCount', 'isStaff'));
    }

    public function createTicket(): View
    {
        return view('admin.modules.support-create');
    }

    public function storeTicket(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'priority' => ['required', 'in:low,normal,high'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'subject' => $data['subject'],
            'priority' => $data['priority'],
            'status' => 'open',
        ]);
        $ticket->messages()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);

        return redirect()->route('admin.support.ticket', $ticket)->with('status', 'Support ticket created successfully.');
    }

    public function showTicket(Request $request, SupportTicket $ticket): View
    {
        $this->authorizeTicket($request, $ticket);
        return view('admin.modules.support-show', [
            'ticket' => $ticket->load(['user', 'messages.user']),
            'isStaff' => $request->user()->hasRole(['super-admin', 'administrator', 'project-manager', 'support-agent']),
        ]);
    }

    public function replyTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorizeTicket($request, $ticket);
        $request->validate(['body' => ['required', 'string', 'max:10000']]);
        $ticket->messages()->create(['user_id' => $request->user()->id, 'body' => $request->string('body')->toString()]);
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }
        return back()->with('status', 'Reply added successfully.');
    }

    public function updateTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('support.manage'), 403);
        $data = $request->validate(['status' => ['required', 'in:open,in-progress,closed'], 'priority' => ['required', 'in:low,normal,high']]);
        $ticket->update($data);
        return back()->with('status', 'Ticket updated successfully.');
    }

    private function authorizeTicket(Request $request, SupportTicket $ticket): void
    {
        $isStaff = $request->user()->hasRole(['super-admin', 'administrator', 'project-manager', 'support-agent']);
        abort_unless($isStaff || $ticket->user_id === $request->user()->id, 403);
    }
}
