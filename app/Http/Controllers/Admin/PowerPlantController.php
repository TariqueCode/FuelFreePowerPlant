<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PowerPlantController extends Controller
{
    public function index(): View
    {
        return view('admin.plants.index', ['plants' => PowerPlant::latest()->paginate(12)]);
    }

    public function create(): View
    {
        return view('admin.plants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:160'],
            'location' => ['nullable','string','max:255'],
            'capacity_kw' => ['nullable','numeric','min:0'],
            'technology' => ['nullable','string','max:160'],
            'status' => ['required','in:planned,operational,maintenance,offline'],
            'overview' => ['nullable','string','max:10000'],
            'started_at' => ['nullable','date'],
            'commissioned_at' => ['nullable','date'],
        ]);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(6));
        PowerPlant::create($data);
        return redirect()->route('admin.plants.index')->with('success', 'Power plant created successfully.');
    }

    public function edit(PowerPlant $plant): View
    {
        return view('admin.plants.edit', ['plant' => $plant]);
    }

    public function update(Request $request, PowerPlant $plant): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:160'],
            'location' => ['nullable','string','max:255'],
            'capacity_kw' => ['nullable','numeric','min:0'],
            'technology' => ['nullable','string','max:160'],
            'status' => ['required','in:planned,operational,maintenance,offline'],
            'overview' => ['nullable','string','max:10000'],
            'started_at' => ['nullable','date'],
            'commissioned_at' => ['nullable','date'],
        ]);
        $plant->update($data);
        return redirect()->route('admin.plants.index')->with('success', 'Power plant updated successfully.');
    }
}
