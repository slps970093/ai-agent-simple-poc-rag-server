<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use Inertia\Inertia;

class AdminPermissionController extends Controller
{
    public function index()
    {
        $permissions = AdminPermission::orderBy('module')
            ->orderBy('action')
            ->get()
            ->map(fn($perm) => [
                'id' => $perm->getKey(),
                'module' => $perm->module,
                'action' => $perm->action,
                'key_name' => $perm->key_name,
                'display_name' => $perm->display_name,
                'description' => $perm->description,
            ]);

        return Inertia::render('AdminPermission/Index', [
            'permissions' => $permissions,
        ]);
    }

    public function create()
    {
        return Inertia::render('AdminPermission/Create');
    }

    public function store(\App\Http\Requests\Admin\AdminPermission\StoreRequest $request)
    {
        AdminPermission::create($request->validated());

        return redirect()->route('admin.permissions.index')
            ->with('success', '權限已建立');
    }

    public function edit(AdminPermission $permission)
    {
        return Inertia::render('AdminPermission/Edit', [
            'permission' => [
                'id' => $permission->getKey(),
                'module' => $permission->module,
                'action' => $permission->action,
                'key_name' => $permission->key_name,
                'display_name' => $permission->getOriginal('display_name'),
                'description' => $permission->getOriginal('description'),
            ],
        ]);
    }

    public function update(\App\Http\Requests\Admin\AdminPermission\UpdateRequest $request, AdminPermission $permission)
    {
        $permission->update($request->validated());

        return redirect()->route('admin.permissions.index')
            ->with('success', '權限已更新');
    }

    public function destroy(AdminPermission $permission)
    {
        $permission->delete();

        return back()->with('success', '權限已刪除');
    }
}
