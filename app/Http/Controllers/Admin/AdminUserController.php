<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUser\StoreRequest as StoreUserRequest;
use App\Http\Requests\Admin\AdminUser\UpdateRequest as UpdateUserRequest;
use App\Models\AdminRole;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = AdminUser::with('profile')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->getKey(),
                    'account' => $user->account,
                    'is_root' => $user->is_root,
                    'status' => $user->status,
                    'last_login_at' => $user->last_login_at?->format('Y-m-d H:i:s'),
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'profile' => $user->profile ? [
                        'name' => $user->profile->name,
                        'phone' => $user->profile->phone,
                    ] : null,
                ];
            });

        return Inertia::render('AdminUser/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $roles = AdminRole::orderBy('name')->get()->map(fn($role) => [
            'id' => $role->getKey(),
            'name' => $role->name,
            'display_name' => $role->display_name,
        ]);

        return Inertia::render('AdminUser/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = AdminUser::create([
            'account' => $validated['account'],
            'password' => Hash::make($validated['password']),
            'is_root' => $validated['is_root'] ?? false,
            'status' => ($validated['is_active'] ?? true) ? AdminStatus::Active : AdminStatus::Inactive,
        ]);

        $user->profile()->create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', '管理者已建立');
    }

    public function edit(AdminUser $user)
    {
        $user->load('profile', 'roles', 'directPermissions');

        $roles = AdminRole::orderBy('name')->get()->map(fn($role) => [
            'id' => $role->getKey(),
            'name' => $role->name,
            'display_name' => $role->display_name,
        ]);

        return Inertia::render('AdminUser/Edit', [
            'user' => [
                'id' => $user->getKey(),
                'account' => $user->account,
                'is_root' => $user->is_root,
                'status' => $user->status,
                'profile' => $user->profile ? [
                    'name' => $user->profile->name,
                    'phone' => $user->profile->phone,
                ] : null,
                'role_ids' => $user->roles->pluck('id')->toArray(),
                'direct_permission_ids' => $user->directPermissions->pluck('id')->toArray(),
            ],
            'roles' => $roles,
        ]);
    }

    public function update(UpdateUserRequest $request, AdminUser $user)
    {
        $validated = $request->validated();

        $user->update([
            'account' => $validated['account'],
            'status' => ($validated['is_active'] ?? true) ? AdminStatus::Active : AdminStatus::Inactive,
        ]);

        if (! empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->profile()->updateOrCreate(
            ['admin_user_id' => $user->getKey()],
            ['name' => $validated['name']]
        );

        $user->roles()->sync($validated['role_ids'] ?? []);
        $user->directPermissions()->sync($validated['direct_permission_ids'] ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', '管理者已更新');
    }

    public function destroy(AdminUser $user)
    {
        if ($user->is_root) {
            return back()->with('error', '無法刪除 root 管理者');
        }

        $user->delete();

        return back()->with('success', '管理者已刪除');
    }
}
