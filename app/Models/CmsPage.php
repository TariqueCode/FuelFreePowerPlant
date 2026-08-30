<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'is_published', 'meta_title', 'meta_description'];

    protected $casts = ['is_published' => 'boolean'];
}
