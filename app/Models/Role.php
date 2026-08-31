<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_system'];

    protected $casts = ['is_system' => 'boolean'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function canBeAssignedBy(User $actor): bool
    {
        if ($actor->hasRole('super-admin')) {
            return true;
        }

        $required = $this->permissions()->pluck('permissions.id');
        if ($required->isEmpty()) {
            return true;
        }

        $available = $actor->roles()
            ->with('permissions:id')
            ->get()
            ->flatMap(fn (Role $role) => $role->permissions->pluck('id'))
            ->unique();

        return $required->diff($available)->isEmpty();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
