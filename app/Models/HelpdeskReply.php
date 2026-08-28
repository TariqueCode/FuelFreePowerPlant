<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpdeskReply extends Model
{
    protected $fillable = [
        'source_type','source_id','admin_user_id','to_email',
        'subject','body','sent_at','status','error',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}