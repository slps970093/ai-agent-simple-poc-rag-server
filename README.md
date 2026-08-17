# Comet Admin Skeleton

基於 Laravel 13 + Inertia.js + Vue 3 + Quasar 的後台管理系統骨架。

## 技術架構

| 層級 | 技術 |
|------|------|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Vue 3, TypeScript |
| UI Framework | Quasar |
| SPA Bridge | Inertia.js |
| CSS | Quasar CSS + Tailwind CSS (前台) |
| Icons | Font Awesome 6 |
| DB | SQLite (可切换 MySQL/PostgreSQL) |
| ORM | Eloquent |
| 多國語言 | spatie/laravel-translatable |
| 路由 | tightenco/ziggy |

## 安裝

```bash
# 複製環境設定
cp .env.example .env

# 安裝 Composer 依賴
composer install

# 安裝 NPM 依賴
npm install

# 產生 APP_KEY
php artisan key:generate

# 資料庫遷移 + Seed
php artisan migrate:fresh --seed

# 啟動開發伺服器
php artisan serve
npm run dev
```

## 預設帳號

| 帳號 | 密碼 | 角色 |
|------|------|------|
| admin | admin123 | Root 最高權限 |

## 目錄結構

```
app/
├── Enums/
│   └── AdminStatus.php          # 管理者狀態列舉
├── Http/
│   ├── Controllers/Admin/       # 後台 Controller
│   ├── Middleware/
│   │   ├── Admin/               # 後台 Inertia Middleware
│   │   └── AdminAuth.php        # 後台認證中間層
│   └── Requests/Admin/          # 表單驗證（按資源分類）
│       ├── Auth/
│       ├── AdminUser/
│       ├── AdminRole/
│       ├── AdminPermission/
│       └── AdminMenu/
├── Models/
│   ├── AdminUser.php
│   ├── AdminUserProfile.php
│   ├── AdminRole.php
│   ├── AdminPermission.php
│   └── AdminMenu.php
└── Providers/
    └── AdminGateServiceProvider.php  # 權限 Gate 註冊

resources/
├── css/
│   ├── app.css                  # 前台 CSS (Tailwind)
│   └── admin.css                # 後台 CSS (Quasar)
├── js/
│   ├── admin/
│   │   ├── app.ts               # 後台入口
│   │   ├── Composables/         # Vue Composables
│   │   ├── Components/          # 共用元件
│   │   ├── Layouts/             # 佈局元件
│   │   └── Pages/               # 頁面元件
│   │       ├── Auth/
│   │       ├── Dashboard.vue
│   │       ├── AdminUser/
│   │       ├── AdminRole/
│   │       ├── AdminPermission/
│   │       └── AdminMenu/
│   └── app.js                   # 前台入口
└── views/admin/                 # Inertia Blade 模板

database/
├── migrations/                  # 資料庫遷移
└── seeders/                     # 資料初始化
```

## 資料表

| 表名 | 說明 |
|------|------|
| `admin_user` | 管理者帳號 |
| `admin_user_profile` | 管理者個人資料 |
| `admin_role` | 角色 |
| `admin_permission` | 權限 |
| `admin_role_permission` | 角色-權限樞紐 |
| `admin_user_role` | 管理者-角色樞紐 |
| `admin_user_permission` | 個人權限（直接指派） |
| `admin_menu` | 後台選單 |

## 路由

| 方法 | 路徑 | 說明 |
|------|------|------|
| GET | `/admin/login` | 登入頁面 |
| POST | `/admin/login` | 登入處理 |
| POST | `/admin/logout` | 登出 |
| GET | `/admin` | 儀表板 |
| CRUD | `/admin/users` | 管理者管理 |
| CRUD | `/admin/roles` | 角色管理 |
| CRUD | `/admin/permissions` | 權限管理 |
| CRUD | `/admin/menus` | 選單管理 |
| POST | `/admin/menus/update-order` | 選單拖拉排序 |

## 權限系統

詳見 [RBAC 權限系統文件](docs/rbac.md)
