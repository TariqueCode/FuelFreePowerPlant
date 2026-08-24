<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PowerPlant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'location',
        'capacity_kw',
        'technology',
        'status',
        'overview',
        'started_at',
        'commissioned_at',
    ];

    protected function casts(): array
    {
        return [
            'capacity_kw' => 'decimal:3',
            'started_at' => 'date',
            'commissioned_at' => 'date',
        ];
    }

    public function performanceData(): HasMany
    {
        return $this->hasMany(PlantPerformance::class);
    }
}
