<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = ['platform', 'label', 'url', 'icon', 'sort_order', 'is_active'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function platformMeta(): array
    {
        return config('fuelfree.social.platforms.' . $this->platform)
            ?: [
                'label' => $this->label,
                'icon' => $this->icon ?: 'fa-solid fa-globe',
                'color' => '#51D8F0',
            ];
    }
}
