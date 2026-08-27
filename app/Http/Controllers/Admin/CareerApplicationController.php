<?php

namespace AppHttpControllersAdmin;

use AppHttpControllersController;
use AppModelsCareerApplication;
use IlluminateHttpRedirectResponse;
use IlluminateHttpRequest;
use IlluminateSupportFacadesStorage;
use IlluminateViewView;

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
}