<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpdeskEmailAttachment extends Model
{
    protected $fillable=['helpdesk_email_id','part','filename','mime_type','size','path'];

    protected function casts(): array { return ['size'=>'integer']; }

    public function email(): BelongsTo { return $this->belongsTo(HelpdeskEmail::class,'helpdesk_email_id'); }
}
