<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagementProfileFolder extends Model
{
    protected $fillable = ['name', 'slug', 'status', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(SiteContentItem::class, 'management_profile_folder_id')
            ->where('type', 'management')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
