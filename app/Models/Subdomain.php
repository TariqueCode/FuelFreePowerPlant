<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subdomain extends Model
{
    protected $fillable = ['user_id','name','target','status','ssl_enabled'];
    protected $casts = ['ssl_enabled' => 'boolean'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
