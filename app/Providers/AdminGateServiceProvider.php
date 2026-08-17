<?php

namespace App\Providers;

use App\Models\AdminPermission;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AdminGateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function ($user) {
            if ($user instanceof AdminUser) {
                return $user->is_root ? true : null;
            }

            return null;
        });

        AdminPermission::all()->each(function ($permission) {
            $gateName = 'admin.' . $permission->name;

            Gate::define($gateName, function ($user) use ($permission) {
                if (! $user instanceof AdminUser) {
                    return false;
                }

                return $user->hasPermission($permission->name);
            });
        });
    }
}
