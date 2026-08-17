# RBAC 權限系統

## 架構概述

採用 RBAC（Role-Based Access Control）模型，結合個人權限指派：

```
AdminUser
├── is_root          → 最高權限，跳過所有檢查
├── roles            → 角色（一對多）
│   └── permissions  → 角色擁有的權限
└── directPermissions → 個人直接指派的權限
```

## 權限判斷流程

```
1. is_root?
   └─ Yes → 允許（ bypass 所有檢查）

2. 查個人權限（admin_user_permission）
   └─ 有該權限？ → 允許

3. 查角色權限（admin_user_role → admin_role_permission）
   └─ 有該權限？ → 允許

4. 以上都沒有 → 拒絕（403）
```

## 資料表結構

### admin_role（角色表）

| 欄位 | 類型 | 說明 |
|------|------|------|
| `id` | bigint | PK |
| `name` | string, unique | 角色識別碼（admin, editor） |
| `display_name` | json | 多國語言顯示名稱 |
| `description` | json, nullable | 多國語言描述 |
| `is_system` | boolean | 系統內建角色不可刪 |

### admin_permission（權限表）

| 欄位 | 類型 | 說明 |
|------|------|------|
| `id` | bigint | PK |
| `name` | string, unique | 權限識別碼（users.create） |
| `display_name` | json | 多國語言顯示名稱 |
| `description` | json, nullable | 多國語言描述 |
| `group` | string, nullable | 權限分組（users, menus） |

### 樞紐表

| 表名 | 欄位 | 說明 |
|------|------|------|
| `admin_role_permission` | `admin_role_id`, `admin_permission_id` | 角色-權限 |
| `admin_user_role` | `admin_user_id`, `admin_role_id` | 管理者-角色 |
| `admin_user_permission` | `admin_user_id`, `admin_permission_id` | 個人權限 |

## Gate 註冊

在 `AdminGateServiceProvider` 中動態從 DB 註冊 Gate：

```php
// app/Providers/AdminGateServiceProvider.php

// is_root 自動 bypass
Gate::before(function ($user) {
    if ($user instanceof AdminUser) {
        return $user->is_root ? true : null;
    }
    return null;
});

// 從 DB 動態註冊每個權限
AdminPermission::all()->each(function ($permission) {
    Gate::define($permission->name, function ($user) use ($permission) {
        if (! $user instanceof AdminUser) {
            return false;
        }
        return $user->hasPermission($permission->name);
    });
});
```

## 使用方式

### 後端：Controller 權限驗證

```php
public function store(StoreUserRequest $request)
{
    $this->authorize('admin.users.create');
    // ...
}

public function update(UpdateUserRequest $request, AdminUser $user)
{
    $this->authorize('admin.users.edit');
    // ...
}

public function destroy(AdminUser $user)
{
    $this->authorize('admin.users.delete');
    // ...
}
```

### 前端：Vue 權限判斷

使用 `useCan()` composable：

```vue
<script setup lang="ts">
import { useCan } from '../../Composables/useCan'

const { can } = useCan()
</script>

<template>
  <!-- 只有擁有 admin.users.create 權限才顯示 -->
  <q-btn v-if="can('admin.users.create')" label="新增" />

  <!-- 只有擁有 admin.users.delete 權限才顯示 -->
  <q-btn v-if="can('admin.users.delete')" label="刪除" />
</template>
```

### 前端：權限資料來源

`HandleInertiaRequests` 會自動 share `can` 陣列：

```php
// 後端
'can' => $admin->is_root
    ? '*'  // root 擁有全部權限
    : $admin->getAllPermissions()->pluck('name')->flip()->toArray(),
```

```typescript
// 前端
const page = usePage()
const can = (permission: string): boolean => {
    const authCan = page.props.auth?.can
    if (!authCan) return false
    if (authCan === '*') return true
    return authCan[permission] === true
}
```

## 權限命名規範

格式：`{模組}.{動作}`

| 權限名稱 | 說明 |
|----------|------|
| `users.index` | 檢視用戶列表 |
| `users.create` | 建立用戶 |
| `users.edit` | 編輯用戶 |
| `users.delete` | 刪除用戶 |
| `roles.index` | 檢視角色列表 |
| `roles.create` | 建立角色 |
| `roles.edit` | 編輯角色 |
| `roles.delete` | 刪除角色 |
| `permissions.index` | 檢視權限列表 |
| `permissions.create` | 建立權限 |
| `permissions.edit` | 編輯權限 |
| `permissions.delete` | 刪除權限 |
| `menus.index` | 檢視選單列表 |
| `menus.create` | 建立選單 |
| `menus.edit` | 編輯選單 |
| `menus.delete` | 刪除選單 |

## 新增權限步驟

### 1. 資料庫新增權限

```php
AdminPermission::create([
    'name' => 'reports.view',
    'display_name' => ['en' => 'View Reports', 'zh_TW' => '檢視報表'],
    'group' => 'reports',
]);
```

### 2. Controller 加入驗證

```php
public function index()
{
    $this->authorize('admin.reports.view');
    // ...
}
```

### 3. Vue 頁面控制 UI

```vue
<q-btn v-if="can('admin.reports.view')" label="查看報表" />
```

### 4. 角色指派權限

在角色管理頁面，將新權限加入角色的權限列表。

## 新增角色步驟

### 1. 資料庫新增角色

```php
$role = AdminRole::create([
    'name' => 'reporter',
    'display_name' => ['en' => 'Reporter', 'zh_TW' => '報表人員'],
]);

// 指派權限
$role->permissions()->attach([
    $reportsView->getKey(),
    $reportsExport->getKey(),
]);
```

### 2. 管理者指派角色

在管理者編輯頁面，將角色加入使用者的角色列表。

## 系統內建角色

`is_system = true` 的角色：
- 編輯頁面中 name 欄位唯讀
- 權限多選唯讀
- 刪除按鈕隱藏
- 無法透過 API 刪除

## 完整流程範例

### 場景：新增「編輯者」角色，只能編輯用戶

1. **建立權限**
   ```
   users.index  → 檢視用戶列表
   users.edit   → 編輯用戶
   ```

2. **建立角色**
   ```
   name: editor
   display_name: 編輯者
   permissions: [users.index, users.edit]
   ```

3. **指派角色給使用者**
   ```
   管理者 → 編輯者角色
   ```

4. **效果**
   - 側邊欄只顯示有權限的選單
   - 新增/刪除按鈕隱藏
   - 直接打 API 會被 Gate 擋掉（403）
