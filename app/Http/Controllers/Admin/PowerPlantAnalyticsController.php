<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use Illuminate\View\View;

class PowerPlantAnalyticsController extends Controller
{
    public function __invoke(PowerPlant $plant): View
    {
        $records = $plant->performanceData()
            ->whereIn('data_status', ['verified', 'real-time'])
            ->latest('measured_at')
            ->limit(90)
            ->get()
            ->sortBy('measured_at')
            ->values();

        $latest = $records->last();
        $maxOutput = max(1, (float) $records->max('power_output_kw'));

        $summary = [
            'output_kw' => $latest?->power_output_kw,
            'energy_kwh' => $latest?->energy_generated_kwh,
            'efficiency' => $latest?->efficiency_percent,
            'uptime' => $latest?->uptime_percent,
        ];

        return view('admin.plants.analytics', compact('plant', 'records', 'latest', 'maxOutput', 'summary'));
    }
}
