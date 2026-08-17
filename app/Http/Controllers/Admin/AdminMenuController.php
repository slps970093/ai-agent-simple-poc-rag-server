<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminMenu\StoreRequest as StoreMenuRequest;
use App\Http\Requests\Admin\AdminMenu\UpdateRequest as UpdateMenuRequest;
use App\Models\AdminMenu;
use Inertia\Inertia;

class AdminMenuController extends Controller
{
    public function index()
    {
        $menus = AdminMenu::whereNull('parent_id')
            ->with(['children' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->getKey(),
                    'name' => $menu->name,
                    'icon' => $menu->icon,
                    'route_name' => $menu->route_name,
                    'sort_order' => $menu->sort_order,
                    'is_active' => $menu->is_active,
                    'children' => $menu->children->map(fn($child) => [
                        'id' => $child->getKey(),
                        'name' => $child->name,
                        'icon' => $child->icon,
                        'route_name' => $child->route_name,
                        'sort_order' => $child->sort_order,
                        'is_active' => $child->is_active,
                    ])->toArray(),
                ];
            });

        return Inertia::render('AdminMenu/Index', [
            'menus' => $menus,
        ]);
    }

    public function create()
    {
        $parents = AdminMenu::whereNull('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($menu) => [
                'id' => $menu->id,
                'name' => $menu->name,
            ]);

        return Inertia::render('AdminMenu/Create', [
            'parents' => $parents,
        ]);
    }

    public function store(StoreMenuRequest $request)
    {
        AdminMenu::create($request->validated());

        return redirect()->route('admin.menus.index')
            ->with('success', '選單已建立');
    }

    public function edit(AdminMenu $menu)
    {
        $parents = AdminMenu::whereNull('parent_id')
            ->where('id', '!=', $menu->getKey())
            ->orderBy('sort_order')
            ->get()
            ->map(fn($m) => [
                'id' => $m->getKey(),
                'name' => $m->name,
            ]);

        return Inertia::render('AdminMenu/Edit', [
            'menu' => [
                'id' => $menu->id,
                'name' => $menu->name,
                'parent_id' => $menu->parent_id,
                'icon' => $menu->icon,
                'route_name' => $menu->route_name,
                'route_params' => $menu->route_params,
                'sort_order' => $menu->sort_order,
                'is_active' => $menu->is_active,
            ],
            'parents' => $parents,
        ]);
    }

    public function update(UpdateMenuRequest $request, AdminMenu $menu)
    {
        $menu->update($request->validated());

        return redirect()->route('admin.menus.index')
            ->with('success', '選單已更新');
    }

    public function destroy(AdminMenu $menu)
    {
        $menu->delete();

        return back()->with('success', '選單已刪除');
    }

    public function updateOrder(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:admin_menu,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            AdminMenu::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return back()->with('success', '排序已更新');
    }
}
