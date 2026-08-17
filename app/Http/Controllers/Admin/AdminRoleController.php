<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use Inertia\Inertia;

class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = AdminRole::withCount('permissions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($role) => [
                'id' => $role->getKey(),
                'display_name' => $role->display_name,
                'description' => $role->description,
                'is_system' => $role->is_system,
                'permissions_count' => $role->permissions_count,
            ]);

        return Inertia::render('AdminRole/Index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $permissions = AdminPermission::orderBy('group')
            ->orderBy('name')
            ->get()
            ->map(fn($perm) => [
                'id' => $perm->getKey(),
                'module' => $perm->module,
                'action' => $perm->action,
                'display_name' => $perm->display_name,
            ]);

        return Inertia::render('AdminRole/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(\App\Http\Requests\Admin\AdminRole\StoreRequest $request)
    {
        $role = AdminRole::create($request->validated());

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', '角色已建立');
    }

    public function edit(AdminRole $role)
    {
        $role->load('permissions');

        return Inertia::render('AdminRole/Edit', [
            'role' => [
                'id' => $role->getKey(),
                'display_name' => $role->display_name,
                'description' => $role->description,
                'is_system' => $role->is_system,
                'permission_ids' => $role->permissions->pluck('id')->toArray(),
            ],
        ]);
    }

    public function update(\App\Http\Requests\Admin\AdminRole\UpdateRequest $request, AdminRole $role)
    {
        $role->update($request->validated());

        if (! $role->is_system) {
            $role->permissions()->sync($request->permissions ?? []);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', '角色已更新');
    }

    public function destroy(AdminRole $role)
    {
        if ($role->is_system) {
            return back()->with('error', '系統內建角色無法刪除');
        }

        $role->delete();

        return back()->with('success', '角色已刪除');
    }
}
