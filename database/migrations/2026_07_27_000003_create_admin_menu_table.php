<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('admin_menu')->nullOnDelete();
            $table->json('name');
            $table->string('icon')->nullable();
            $table->string('route_name')->nullable();
            $table->json('route_params')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['parent_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_menu');
    }
};
