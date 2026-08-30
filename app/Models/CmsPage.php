<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'is_published', 'meta_title', 'meta_description', 'builder_blocks', 'template'];

    protected $casts = ['is_published' => 'boolean', 'builder_blocks' => 'array'];
}
