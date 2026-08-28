<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerApplication;
use App\Services\HelpDeskReplyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CareerApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = CareerApplication::query()
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.career-applications.index', compact('applications'));
    }

    public function show(CareerApplication $application): View
    {
        return view('admin.career-applications.show', compact('application'));
    }

    public function update(Request $request, CareerApplication $application): RedirectResponse
    {
        $data = $request->validate(['status' => ['required','in:new,reviewing,shortlisted,rejected,hired']]);
        $application->update($data);
        return back()->with('status','Application status updated.');
    }

    public function download(CareerApplication $application)
    {
        abort_unless(Storage::disk('local')->exists($application->cv_path), 404);
        return Storage::disk('local')->download($application->cv_path, $application->cv_original_name ?: basename($application->cv_path));
    }
    public function reply(Request $request, CareerApplication $application, HelpDeskReplyService $replyService): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:50000']]);
        try {
            $replyService->sendCareerApplication($application, $data['body']);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['reply' => 'Reply could not be sent: '.($e->getMessage() ?: 'mail server error.')])->withInput();
        }
        $application->update(['status' => 'reviewing']);
        return back()->with('status', 'Reply sent successfully.');
    }

}