<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_key')->unique();
            $table->string('channel')->default('discord'); // discord / line / telegram
            $table->text('identity')->nullable();
            $table->text('allowed_actions')->nullable();
            $table->text('restricted_actions')->nullable();
            $table->text('forbidden_actions')->nullable();
            $table->string('status')->default('active'); // active / inactive
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_bots');
    }
};
