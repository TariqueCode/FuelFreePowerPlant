<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = ['key', 'label', 'description', 'source_type', 'is_enabled', 'sort_order', 'settings'];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'sort_order' => 'integer',
            'settings' => 'array',
        ];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
