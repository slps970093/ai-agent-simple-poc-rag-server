<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class AdminPermission extends Model
{
    use HasTranslations;

    protected $table = 'admin_permission';

    protected $fillable = [
        'module',
        'action',
        'display_name',
        'description',
    ];

    public array $translatable = ['display_name', 'description'];

    public function getKeyNameAttribute(): string
    {
        return $this->module . '.' . $this->action;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_permission');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(AdminUser::class, 'admin_user_permission');
    }
}
