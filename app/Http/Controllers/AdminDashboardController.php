<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\PlantPerformance;
use App\Models\PowerPlant;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $isFullAdmin = $user->hasRole(['super-admin', 'administrator']);
        $users = $isFullAdmin ? User::count() : null;
        $documents = Document::where('user_id', $user->id)->count();
        $folders = DocumentFolder::where('user_id', $user->id)->count();
        $storageBytes = collect(Storage::disk('local')->allFiles("private/{$user->id}"))
            ->reject(fn (string $path) => str_contains($path, '/.uploads/'))
            ->sum(fn (string $path) => (int) Storage::disk('local')->size($path));

        $plantStats = [
            'total' => PowerPlant::count(),
            'operational' => PowerPlant::where('status', 'operational')->count(),
            'planned' => PowerPlant::where('status', 'planned')->count(),
            'maintenance' => PowerPlant::where('status', 'maintenance')->count(),
            'offline' => PowerPlant::where('status', 'offline')->count(),
            'capacity_kw' => (float) PowerPlant::sum('capacity_kw'),
        ];

        $verifiedQuery = PlantPerformance::query()
            ->whereIn('data_status', ['verified', 'real-time']);

        $latestPerformance = (clone $verifiedQuery)
            ->latest('measured_at')
            ->first();

        $performanceSummary = [
            'output_kw' => $latestPerformance?->power_output_kw,
            'energy_kwh' => $latestPerformance?->energy_generated_kwh,
            'efficiency' => $latestPerformance?->efficiency_percent,
            'uptime' => $latestPerformance?->uptime_percent,
        ];

        $performanceTrend = (clone $verifiedQuery)
            ->where('measured_at', '>=', now()->subDays(13)->startOfDay())
            ->orderBy('measured_at')
            ->get(['measured_at', 'power_output_kw', 'energy_generated_kwh'])
            ->groupBy(fn (PlantPerformance $record) => $record->measured_at->format('Y-m-d'))
            ->map(fn ($records, $date) => [
                'date' => $date,
                'label' => date('d M', strtotime($date)),
                'output_kw' => (float) $records->avg('power_output_kw'),
                'energy_kwh' => (float) $records->sum('energy_generated_kwh'),
            ])
            ->values();

        $performanceTrendMax = max(1, (float) $performanceTrend->max('output_kw'));
        $verifiedRecordCount = (clone $verifiedQuery)->count();

        return view('admin.dashboard', compact(
            'users', 'documents', 'folders', 'storageBytes',
            'plantStats', 'performanceSummary', 'latestPerformance',
            'performanceTrend', 'performanceTrendMax', 'verifiedRecordCount'
        ));
    }
}
