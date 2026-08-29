<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class CareerApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = CareerApplication::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.career-applications.index', compact('applications'));
    }

    public function show(CareerApplication $application): RedirectResponse
    {
        return redirect()->route('admin.helpdesk.show', ['type' => 'career', 'id' => $application->id]);
    }

    public function update(Request $request, CareerApplication $application): Response
    {
        $data = $request->validate(['status' => ['required','in:new,reviewing,shortlisted,rejected,hired']]);
        $application->update($data);

        return response()->redirectToRoute('admin.career-applications.show', $application)
            ->with('status', 'Application status updated.');
    }

    public function download(CareerApplication $application)
    {
        abort_unless(Storage::disk('local')->exists($application->cv_path), 404);

        return Storage::disk('local')->download(
            $application->cv_path,
            $application->cv_original_name ?: basename($application->cv_path)
        );
    }
}
