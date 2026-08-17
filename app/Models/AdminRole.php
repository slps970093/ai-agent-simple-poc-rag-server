<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class AdminRole extends Model
{
    use HasTranslations;

    protected $table = 'admin_role';

    protected $fillable = [
        'display_name',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public array $translatable = ['display_name', 'description'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_role_permission');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(AdminUser::class, 'admin_user_role');
    }
}
