<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAccount extends Model
{
    protected $fillable = ['user_id','address','display_name','status','imap_host','imap_port','smtp_host','smtp_port','username','password'];
    protected $casts = ['password' => 'encrypted'];
    protected $hidden = ['password'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
