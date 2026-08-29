<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelpdeskEmail extends Model
{
    protected $fillable=[
        'email_account_id','mailbox_group','external_uid','message_id','fingerprint','sender_name','sender_email',
        'to_email','cc_email','subject','body_html','body_text','status','received_at','imported_at','external_deleted_at','last_error',
    ];

    protected function casts(): array
    {
        return ['external_uid'=>'integer','received_at'=>'datetime','imported_at'=>'datetime','external_deleted_at'=>'datetime'];
    }

    public function account(): BelongsTo { return $this->belongsTo(EmailAccount::class,'email_account_id'); }
    public function attachments(): HasMany { return $this->hasMany(HelpdeskEmailAttachment::class); }
}
