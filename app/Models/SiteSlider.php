<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSlider extends Model
{
    protected $fillable = ['title','image_path','link_url','sort_order','is_published','starts_at','ends_at'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_published', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderBy('sort_order')->orderByDesc('id');
    }
}
