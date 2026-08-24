<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantPerformance extends Model
{
    protected $table = 'plant_performance';

    protected $fillable = [
        'power_plant_id',
        'measured_at',
        'power_output_kw',
        'energy_generated_kwh',
        'efficiency_percent',
        'uptime_percent',
        'environmental_metrics',
        'data_status',
        'source',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'measured_at' => 'datetime',
            'power_output_kw' => 'decimal:3',
            'energy_generated_kwh' => 'decimal:3',
            'efficiency_percent' => 'decimal:3',
            'uptime_percent' => 'decimal:3',
            'environmental_metrics' => 'array',
        ];
    }

    public function powerPlant(): BelongsTo
    {
        return $this->belongsTo(PowerPlant::class);
    }
}
