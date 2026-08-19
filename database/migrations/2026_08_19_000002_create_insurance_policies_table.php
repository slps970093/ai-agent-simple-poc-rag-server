<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_company_id')
                ->constrained('insurance_companies')
                ->restrictOnDelete();
            $table->string('policy_code', 20);
            $table->string('name');
            $table->string('version')->default('1.0');
            $table->date('effective_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['insurance_company_id', 'policy_code']);
            $table->index(['insurance_company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
