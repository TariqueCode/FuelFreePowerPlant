<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteContentItem extends Model
{
    protected $fillable = [
        'type', 'title', 'slug', 'excerpt', 'designation', 'phone', 'email',
        'content', 'image_path', 'visiting_card_path', 'status', 'sort_order', 'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'sort_order' => 'integer'];
    }

    public function galleryMedia(): HasMany
    {
        return $this->hasMany(GalleryMedia::class, 'gallery_id')->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->where('type', 'gallery')
                    ->orWhere(function ($q) {
                        $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                    });
            });
    }
}
