<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // vector DDL must run outside a transaction so PostgreSQL resolves the
    // vector type (registered by the extension) before executing the statement.
    public $withinTransaction = false;

    private const EMBEDDING_DIMENSIONS = 512;

    public function up(): void
    {
        $usesPgsql = DB::getDriverName() === 'pgsql';

        Schema::create('insurance_policy_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_policy_id')
                ->constrained('insurance_policies')
                ->restrictOnDelete();
            $table->text('content');
            $table->text('embedding')->nullable();
            $table->string('embedding_model')->default('BAAI/bge-small-zh-v1.5');
            $table->unsignedInteger('page_from')->nullable();
            $table->unsignedInteger('page_to')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('source_metadata')->nullable();
            $table->timestamps();

            $table->index(['insurance_policy_id', 'sort_order']);
        });

        if ($usesPgsql) {
            // Alter the column to the proper vector type using raw SQL.
            // This avoids relying on Blueprint macro resolution within a transaction.
            DB::statement(sprintf(
                'ALTER TABLE insurance_policy_contents ALTER COLUMN embedding TYPE vector(%d) USING embedding::vector(%d)',
                self::EMBEDDING_DIMENSIONS,
                self::EMBEDDING_DIMENSIONS
            ));

            DB::statement(
                'CREATE INDEX insurance_policy_contents_embedding_hnsw_idx '
                .'ON insurance_policy_contents USING hnsw (embedding vector_cosine_ops)'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_policy_contents');
    }
};
