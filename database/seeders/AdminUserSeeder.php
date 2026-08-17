<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = AdminUser::updateOrCreate(
            ['account' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'is_root' => true,
                'status' => \App\Enums\AdminStatus::Active,
            ]
        );

        $user->profile()->updateOrCreate(
            ['admin_user_id' => $user->id],
            ['name' => 'Root Admin']
        );
    }
}
