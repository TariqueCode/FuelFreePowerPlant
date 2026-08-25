<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContentItem extends Model
{
    protected $fillable = [
        'type', 'title', 'slug', 'excerpt', 'content', 'image_path',
        'status', 'sort_order', 'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'sort_order' => 'integer'];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) { $q->whereNull('published_at')->orWhere('published_at', '<=', now()); });
    }
}
