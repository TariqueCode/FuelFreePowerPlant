<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpDeskReply extends Model
{
    protected $fillable = [
        'email_account_id','inquiry_id','career_application_id','from_address',
        'to_address','subject','body','status','error_message','sent_at',
    ];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function careerApplication(): BelongsTo
    {
        return $this->belongsTo(CareerApplication::class);
    }
}
