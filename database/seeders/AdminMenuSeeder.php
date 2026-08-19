<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use Illuminate\Database\Seeder;

class AdminMenuSeeder extends Seeder
{
    public function run(): void
    {
        AdminMenu::query()->delete();

        $settings = AdminMenu::create([
            'name' => ['en' => 'Admin Settings', 'zh_TW' => '管理員設定'],
            'icon' => 'fas fa-gear',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        AdminMenu::create([
            'parent_id' => $settings->getKey(),
            'name' => ['en' => 'User Management', 'zh_TW' => '用戶管理'],
            'icon' => 'fas fa-users',
            'route_name' => 'admin.users.index',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        AdminMenu::create([
            'parent_id' => $settings->getKey(),
            'name' => ['en' => 'Menu Management', 'zh_TW' => '選單管理'],
            'icon' => 'fas fa-bars',
            'route_name' => 'admin.menus.index',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        AdminMenu::create([
            'parent_id' => $settings->getKey(),
            'name' => ['en' => 'Role Management', 'zh_TW' => '角色管理'],
            'icon' => 'fas fa-user-tag',
            'route_name' => 'admin.roles.index',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        AdminMenu::create([
            'parent_id' => $settings->getKey(),
            'name' => ['en' => 'Permission Management', 'zh_TW' => '權限管理'],
            'icon' => 'fas fa-shield-halved',
            'route_name' => 'admin.permissions.index',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $botManagement = AdminMenu::create([
            'name' => ['en' => 'Bot Management', 'zh_TW' => '機器人管理'],
            'icon' => 'fas fa-robot',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        AdminMenu::create([
            'parent_id' => $botManagement->getKey(),
            'name' => ['en' => 'Bots', 'zh_TW' => '機器人列表'],
            'icon' => 'fas fa-robot',
            'route_name' => 'admin.ai-bots.index',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $insuranceManagement = AdminMenu::create([
            'name' => ['en' => 'Insurance Management', 'zh_TW' => '保險管理'],
            'icon' => 'fas fa-file-shield',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        foreach ([
            ['Insurance Companies', '保險公司', 'fas fa-building-shield', 'admin.insurance-companies.index'],
            ['Insurance Policies', '保單', 'fas fa-file-contract', 'admin.insurance-policies.index'],
            ['Policy Contents', '保單內文', 'fas fa-align-left', 'admin.insurance-policy-contents.index'],
        ] as $index => [$englishName, $traditionalChineseName, $icon, $routeName]) {
            AdminMenu::create([
                'parent_id' => $insuranceManagement->getKey(),
                'name' => ['en' => $englishName, 'zh_TW' => $traditionalChineseName],
                'icon' => $icon,
                'route_name' => $routeName,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
}
