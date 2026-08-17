<?php

namespace App\Models;

use App\Enums\AdminStatus;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $table = 'admin_user';

    protected $fillable = ['account', 'password', 'is_root', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_root' => 'boolean',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'status' => AdminStatus::class,
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(AdminUserProfile::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'admin_user_role');
    }

    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_user_permission');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_root) {
            return true;
        }

        if ($this->directPermissions()->where('name', $permission)->exists()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('name', $permission))
            ->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function getAllPermissions()
    {
        return AdminPermission::whereHas('roles', fn($q) => $q->whereIn('id', $this->roles->pluck('id')))
            ->orWhereHas('users', fn($q) => $q->where('admin_user_id', $this->getKey()))
            ->get();
    }

    protected static function booted(): void
    {
        static::created(function (AdminUser $admin) {
            $admin->profile()->create(['name' => $admin->account]);
        });

        static::deleting(function (AdminUser $admin) {
            if ($admin->is_root) {
                throw new \RuntimeException('Root admin cannot be deleted.');
            }
        });
    }
}
