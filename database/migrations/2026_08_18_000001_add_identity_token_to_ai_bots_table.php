<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_bots', function (Blueprint $table) {
            $table->string('identity_token')->nullable()->unique();
        });

        DB::table('ai_bots')->whereNull('identity_token')->orderBy('id')->each(function ($bot) {
            DB::table('ai_bots')->where('id', $bot->id)->update([
                'identity_token' => 'identity_'.Str::random(40),
            ]);
        });

        DB::statement('ALTER TABLE ai_bots ALTER COLUMN identity_token SET NOT NULL');
    }

    public function down(): void
    {
        Schema::table('ai_bots', function (Blueprint $table) {
            $table->dropUnique(['identity_token']);
            $table->dropColumn('identity_token');
        });
    }
};
