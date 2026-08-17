<?php

use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AiBotController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->middleware(\App\Http\Middleware\Admin\HandleInertiaRequests::class)->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.store');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Users
        Route::get('users', [AdminUserController::class, 'index'])
            ->middleware('admin.permission:admin.users.index')
            ->name('admin.users.index');
        Route::get('users/create', [AdminUserController::class, 'create'])
            ->middleware('admin.permission:admin.users.create')
            ->name('admin.users.create');
        Route::post('users', [AdminUserController::class, 'store'])
            ->middleware('admin.permission:admin.users.create')
            ->name('admin.users.store');
        Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])
            ->middleware('admin.permission:admin.users.edit')
            ->name('admin.users.edit');
        Route::put('users/{user}', [AdminUserController::class, 'update'])
            ->middleware('admin.permission:admin.users.edit')
            ->name('admin.users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])
            ->middleware('admin.permission:admin.users.delete')
            ->name('admin.users.destroy');

        // Roles
        Route::get('roles', [AdminRoleController::class, 'index'])
            ->middleware('admin.permission:admin.roles.index')
            ->name('admin.roles.index');
        Route::get('roles/create', [AdminRoleController::class, 'create'])
            ->middleware('admin.permission:admin.roles.create')
            ->name('admin.roles.create');
        Route::post('roles', [AdminRoleController::class, 'store'])
            ->middleware('admin.permission:admin.roles.create')
            ->name('admin.roles.store');
        Route::get('roles/{role}/edit', [AdminRoleController::class, 'edit'])
            ->middleware('admin.permission:admin.roles.edit')
            ->name('admin.roles.edit');
        Route::put('roles/{role}', [AdminRoleController::class, 'update'])
            ->middleware('admin.permission:admin.roles.edit')
            ->name('admin.roles.update');
        Route::delete('roles/{role}', [AdminRoleController::class, 'destroy'])
            ->middleware('admin.permission:admin.roles.delete')
            ->name('admin.roles.destroy');

        // Permissions
        Route::get('permissions', [AdminPermissionController::class, 'index'])
            ->middleware('admin.permission:admin.permissions.index')
            ->name('admin.permissions.index');
        Route::get('permissions/create', [AdminPermissionController::class, 'create'])
            ->middleware('admin.permission:admin.permissions.create')
            ->name('admin.permissions.create');
        Route::post('permissions', [AdminPermissionController::class, 'store'])
            ->middleware('admin.permission:admin.permissions.create')
            ->name('admin.permissions.store');
        Route::get('permissions/{permission}/edit', [AdminPermissionController::class, 'edit'])
            ->middleware('admin.permission:admin.permissions.edit')
            ->name('admin.permissions.edit');
        Route::put('permissions/{permission}', [AdminPermissionController::class, 'update'])
            ->middleware('admin.permission:admin.permissions.edit')
            ->name('admin.permissions.update');
        Route::delete('permissions/{permission}', [AdminPermissionController::class, 'destroy'])
            ->middleware('admin.permission:admin.permissions.delete')
            ->name('admin.permissions.destroy');

        // AI Bots
        Route::get('ai-bots', [AiBotController::class, 'index'])
            ->middleware('admin.permission:admin.ai-bots.index')
            ->name('admin.ai-bots.index');
        Route::get('ai-bots/create', [AiBotController::class, 'create'])
            ->middleware('admin.permission:admin.ai-bots.create')
            ->name('admin.ai-bots.create');
        Route::post('ai-bots', [AiBotController::class, 'store'])
            ->middleware('admin.permission:admin.ai-bots.create')
            ->name('admin.ai-bots.store');
        Route::get('ai-bots/{aiBot}/edit', [AiBotController::class, 'edit'])
            ->middleware('admin.permission:admin.ai-bots.edit')
            ->name('admin.ai-bots.edit');
        Route::put('ai-bots/{aiBot}', [AiBotController::class, 'update'])
            ->middleware('admin.permission:admin.ai-bots.edit')
            ->name('admin.ai-bots.update');
        Route::delete('ai-bots/{aiBot}', [AiBotController::class, 'destroy'])
            ->middleware('admin.permission:admin.ai-bots.delete')
            ->name('admin.ai-bots.destroy');

        // Menus
        Route::get('menus', [AdminMenuController::class, 'index'])
            ->middleware('admin.permission:admin.menus.index')
            ->name('admin.menus.index');
        Route::get('menus/create', [AdminMenuController::class, 'create'])
            ->middleware('admin.permission:admin.menus.create')
            ->name('admin.menus.create');
        Route::post('menus', [AdminMenuController::class, 'store'])
            ->middleware('admin.permission:admin.menus.create')
            ->name('admin.menus.store');
        Route::get('menus/{menu}/edit', [AdminMenuController::class, 'edit'])
            ->middleware('admin.permission:admin.menus.edit')
            ->name('admin.menus.edit');
        Route::put('menus/{menu}', [AdminMenuController::class, 'update'])
            ->middleware('admin.permission:admin.menus.edit')
            ->name('admin.menus.update');
        Route::delete('menus/{menu}', [AdminMenuController::class, 'destroy'])
            ->middleware('admin.permission:admin.menus.delete')
            ->name('admin.menus.destroy');
        Route::post('menus/update-order', [AdminMenuController::class, 'updateOrder'])
            ->middleware('admin.permission:admin.menus.edit')
            ->name('admin.menus.update-order');
    });
});
