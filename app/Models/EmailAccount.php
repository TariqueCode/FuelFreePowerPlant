<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAccount extends Model
{
    protected $fillable = [
        'user_id','address','display_name','mailbox_group','status','imap_host',
        'imap_port','smtp_host','smtp_port','username','password','provisioned','provider_message',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'provisioned' => 'boolean',
            'imap_port' => 'integer',
            'smtp_port' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}