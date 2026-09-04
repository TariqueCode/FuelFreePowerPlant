<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenuItem extends Model
{
    protected $fillable = [
        'menu', 'group', 'parent_id', 'label', 'label_override', 'url', 'route_name', 'target',
        'icon', 'is_visible', 'sort_order', 'source_key', 'source_type',
        'area', 'permission_key',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            if ($item->source_type === 'folder' || app()->runningInConsole()) {
                return;
            }

            $requestedLabel = trim((string) request()->input('label', ''));
            if ($requestedLabel === '') {
                return;
            }

            $item->label_override = $requestedLabel === trim((string) $item->label)
                ? null
                : $requestedLabel;
        });
    }

    public function displayLabel(): string
    {
        if ($this->label_override !== null && trim((string) $this->label_override) !== '') {
            return (string) $this->label_override;
        }

        if ($this->route_name === 'site.plants' || trim((string) $this->url, '/') === 'plants') {
            return (string) config('fuelfree.projects.label', 'Projects & Our Plans');
        }

        return (string) $this->label;
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
