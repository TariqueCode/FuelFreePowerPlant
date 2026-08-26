<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryMedia extends Model
{
    protected $fillable = ['gallery_id', 'type', 'path', 'original_name', 'sort_order'];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(SiteContentItem::class, 'gallery_id');
    }
}
