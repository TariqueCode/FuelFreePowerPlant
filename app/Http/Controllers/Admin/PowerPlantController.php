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
    public function index(): View { return view('admin.plants.index', ['plants'=>PowerPlant::latest()->paginate(12)]); }
    public function create(): View { return view('admin.plants.create'); }

    public function store(Request $request): RedirectResponse
    {
        PowerPlant::create($this->validated($request) + ['slug'=>Str::slug($request->string('name')).'-'.Str::lower(Str::random(6))]);
        return redirect()->route('admin.plants.index')->with('success','Power plant created successfully.');
    }

    public function edit(PowerPlant $plant): View { return view('admin.plants.edit', ['plant'=>$plant]); }

    public function update(Request $request, PowerPlant $plant): RedirectResponse
    {
        $plant->update($this->validated($request));
        return redirect()->route('admin.plants.index')->with('success','Power plant updated successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'=>['required','string','max:160'],'location'=>['nullable','string','max:255'],'address'=>['nullable','string','max:500'],
            'capacity_kw'=>['nullable','numeric','min:0'],'annual_generation_mwh'=>['nullable','numeric','min:0'],
            'efficiency_percent'=>['nullable','numeric','min:0','max:100'],'co2_reduction_tonnes'=>['nullable','numeric','min:0'],
            'land_area_acres'=>['nullable','numeric','min:0'],'technology'=>['nullable','string','max:160'],'fuel_type'=>['nullable','string','max:120'],
            'ownership'=>['nullable','string','max:160'],'operator'=>['nullable','string','max:160'],
            'latitude'=>['nullable','numeric','between:-90,90'],'longitude'=>['nullable','numeric','between:-180,180'],
            'status'=>['required','in:planned,operational,maintenance,offline'],'overview'=>['nullable','string','max:10000'],
            'image_path'=>['nullable','string','max:500'],'contact_email'=>['nullable','email','max:255'],'contact_phone'=>['nullable','string','max:50'],
            'started_at'=>['nullable','date'],'commissioned_at'=>['nullable','date'],
        ]);
    }
}
