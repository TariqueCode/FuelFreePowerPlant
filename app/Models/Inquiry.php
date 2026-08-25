<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'status',
        'admin_note', 'read_at', 'resolved_at',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime', 'resolved_at' => 'datetime'];
    }
}
