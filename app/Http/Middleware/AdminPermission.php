<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        $admin = $request->user('admin');

        if (! $admin) {
            return redirect()->route('admin.login');
        }

        if ($admin->is_root) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            $name = str_starts_with($permission, 'admin.')
                ? substr($permission, 6)
                : $permission;

            if ($admin->hasPermission($name)) {
                return $next($request);
            }
        }

        abort(403, '權限不足');
    }
}
