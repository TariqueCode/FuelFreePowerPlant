<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenuItem extends Model
{
    protected $fillable = [
        'menu', 'group', 'parent_id', 'label', 'url', 'route_name', 'target',
        'icon', 'is_visible', 'sort_order', 'source_key', 'source_type',
        'area', 'permission_key',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function displayLabel(): string
    {
        $label = trim((string) $this->label);

        // Preserve the project-wide default for the legacy Plants source while
        // allowing administrators to explicitly choose a different navigation label.
        if (($this->route_name === 'site.plants' || trim((string) $this->url, '/') === 'plants')
            && ($label === '' || strcasecmp($label, 'Plants') === 0)) {
            return (string) config('fuelfree.projects.label', 'Projects & Our Plans');
        }

        return $label;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function isFolder(): bool
    {
        return $this->source_type === 'folder';
    }

    public function isDestination(): bool
    {
        return in_array($this->source_type, ['route', 'cms_page', 'external_link'], true);
    }
}
