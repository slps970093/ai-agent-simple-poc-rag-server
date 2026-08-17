<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_role_permission', function (Blueprint $table) {
            $table->foreignId('admin_role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['admin_role_id', 'admin_permission_id']);
        });

        Schema::create('admin_user_role', function (Blueprint $table) {
            $table->foreignId('admin_user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_role_id')->constrained()->cascadeOnDelete();
            $table->primary(['admin_user_id', 'admin_role_id']);
        });

        Schema::create('admin_user_permission', function (Blueprint $table) {
            $table->foreignId('admin_user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['admin_user_id', 'admin_permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_role_permission');
        Schema::dropIfExists('admin_user_role');
        Schema::dropIfExists('admin_user_permission');
    }
};
