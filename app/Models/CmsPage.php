<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'is_published', 'meta_title', 'meta_description', 'builder_blocks', 'template', 'use_global_framework', 'use_global_header', 'use_global_footer'];

    protected $casts = ['is_published' => 'boolean', 'builder_blocks' => 'array', 'use_global_framework' => 'boolean', 'use_global_header' => 'boolean', 'use_global_footer' => 'boolean'];
}
