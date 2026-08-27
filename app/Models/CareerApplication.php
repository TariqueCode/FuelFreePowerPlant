<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerApplication extends Model
{
    protected $fillable = [
        'name','email','phone','position','education','experience',
        'location','message','cv_path','cv_original_name','status','consent',
    ];

    protected function casts(): array
    {
        return ['consent' => 'boolean'];
    }
}