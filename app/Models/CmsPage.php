<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];
}
