<?php

namespace App\Http\Middleware\Admin;

use App\Models\AdminMenu;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'admin.app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $admin = $request->user('admin');

        return [
            ...parent::share($request),
            'auth' => [
                'admin' => $admin
                    ? $admin->only('id', 'account', 'is_root', 'status')
                    : null,
                'can' => $admin
                    ? ($admin->is_root ? '*' : $admin->getAllPermissions()->mapWithKeys(fn($p) => ['admin.' . $p->name => true])->toArray())
                    : [],
            ],
            'menu' => $admin
                ? $this->getMenuTree()
                : [],
        ];
    }

    private function getMenuTree(): array
    {
        return AdminMenu::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['allChildren' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($menu) {
                return $this->formatMenu($menu);
            })
            ->toArray();
    }

    private function formatMenu($menu): array
    {
        return [
            'id' => $menu->getKey(),
            'name' => $menu->name,
            'icon' => $menu->icon,
            'route_name' => $menu->route_name,
            'url' => $menu->url,
            'children' => $menu->allChildren->map(function ($child) {
                return $this->formatMenu($child);
            })->toArray(),
        ];
    }
}
