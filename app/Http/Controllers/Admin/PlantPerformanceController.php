<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlantPerformance;
use App\Models\PowerPlant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlantPerformanceController extends Controller
{
    public function index(PowerPlant $plant): View
    {
        return view('admin.plants.performance.index', [
            'plant' => $plant,
            'records' => $plant->performanceData()->latest('measured_at')->paginate(20),
        ]);
    }

    public function store(Request $request, PowerPlant $plant): RedirectResponse
    {
        $data = $request->validate([
            'measured_at' => ['required','date'],
            'power_output_kw' => ['nullable','numeric','min:0'],
            'energy_generated_kwh' => ['nullable','numeric','min:0'],
            'efficiency_percent' => ['nullable','numeric','between:0,100'],
            'uptime_percent' => ['nullable','numeric','between:0,100'],
            'environmental_metrics' => ['nullable','json'],
            'data_status' => ['required','in:demonstration,estimated,verified,real-time,target'],
            'source' => ['nullable','string','max:255'],
            'notes' => ['nullable','string','max:5000'],
        ]);

        if (!empty($data['environmental_metrics'])) {
            $data['environmental_metrics'] = json_decode($data['environmental_metrics'], true, 512, JSON_THROW_ON_ERROR);
        }

        $plant->performanceData()->create($data);

        return back()->with('success', 'Performance record added successfully.');
    }

    public function destroy(PowerPlant $plant, PlantPerformance $performance): RedirectResponse
    {
        abort_unless($performance->power_plant_id === $plant->id, 404);
        $performance->delete();

        return back()->with('success', 'Performance record deleted successfully.');
    }
}
