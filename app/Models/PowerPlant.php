<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PowerPlant extends Model
{
    protected $fillable = [
        'name','slug','location','address','capacity_kw','annual_generation_mwh','efficiency_percent',
        'co2_reduction_tonnes','land_area_acres','technology','fuel_type','ownership','operator',
        'latitude','longitude','status','overview','image_path','contact_email','contact_phone',
        'started_at','commissioned_at',
    ];

    protected function casts(): array
    {
        return [
            'capacity_kw'=>'decimal:3','annual_generation_mwh'=>'decimal:3','efficiency_percent'=>'decimal:3',
            'co2_reduction_tonnes'=>'decimal:3','land_area_acres'=>'decimal:3','latitude'=>'decimal:7','longitude'=>'decimal:7',
            'started_at'=>'date','commissioned_at'=>'date',
        ];
    }

    public function performanceData(): HasMany
    {
        return $this->hasMany(PlantPerformance::class);
    }
}
