<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        AdminPermission::query()->delete();

        $modules = [
            'users' => ['index', 'create', 'edit', 'delete'],
            'roles' => ['index', 'create', 'edit', 'delete'],
            'permissions' => ['index', 'create', 'edit', 'delete'],
            'menus' => ['index', 'create', 'edit', 'delete'],
            'ai-bots' => ['index', 'create', 'edit', 'delete'],
            'insurance-companies' => ['index', 'create', 'edit', 'delete'],
            'insurance-policies' => ['index', 'create', 'edit', 'delete'],
            'insurance-policy-contents' => ['index', 'create', 'edit', 'delete'],
        ];

        $displayNames = [
            'users' => ['en' => 'User', 'zh_TW' => '用戶'],
            'roles' => ['en' => 'Role', 'zh_TW' => '角色'],
            'permissions' => ['en' => 'Permission', 'zh_TW' => '權限'],
            'menus' => ['en' => 'Menu', 'zh_TW' => '選單'],
            'ai-bots' => ['en' => 'AI Bot', 'zh_TW' => 'AI 機器人'],
            'insurance-companies' => ['en' => 'Insurance Company', 'zh_TW' => '保險公司'],
            'insurance-policies' => ['en' => 'Insurance Policy', 'zh_TW' => '保單'],
            'insurance-policy-contents' => ['en' => 'Policy Content', 'zh_TW' => '保單內文'],
        ];

        $actionNames = [
            'index' => ['en' => 'View', 'zh_TW' => '檢視'],
            'create' => ['en' => 'Create', 'zh_TW' => '建立'],
            'edit' => ['en' => 'Edit', 'zh_TW' => '編輯'],
            'delete' => ['en' => 'Delete', 'zh_TW' => '刪除'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                AdminPermission::create([
                    'module' => $module,
                    'action' => $action,
                    'display_name' => [
                        'en' => $displayNames[$module]['en'].' '.$actionNames[$action]['en'],
                        'zh_TW' => $displayNames[$module]['zh_TW'].$actionNames[$action]['zh_TW'],
                    ],
                ]);
            }
        }
    }
}
