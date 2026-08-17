<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class AdminMenu extends Model
{
    use HasTranslations;

    protected $table = 'admin_menu';

    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'route_name',
        'route_params',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'route_params' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public array $translatable = ['name'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AdminMenu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AdminMenu::class, 'parent_id')->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->route_name) {
            return null;
        }

        try {
            return route($this->route_name, $this->route_params ?? []);
        } catch (\Exception $e) {
            return null;
        }
    }
}
