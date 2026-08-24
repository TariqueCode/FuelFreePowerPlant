<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'is_sensitive'];

    protected $casts = ['is_sensitive' => 'boolean'];
}
