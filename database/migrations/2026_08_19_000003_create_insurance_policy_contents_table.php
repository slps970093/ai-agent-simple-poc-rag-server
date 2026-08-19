<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // CREATE EXTENSION and vector DDL cannot run inside a PostgreSQL transaction
    // because the new type is only visible after the extension is committed.
    public $withinTransaction = false;

    private const EMBEDDING_DIMENSIONS = 512;

    public function up(): void
    {
        $usesPgsql = DB::getDriverName() === 'pgsql';

        if ($usesPgsql) {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
        }

        Schema::create('insurance_policy_contents', function (Blueprint $table) use ($usesPgsql) {
            $table->id();
            $table->foreignId('insurance_policy_id')
                ->constrained('insurance_policies')
                ->restrictOnDelete();
            $table->text('content');

            if ($usesPgsql) {
                $table->vector('embedding', self::EMBEDDING_DIMENSIONS)->nullable();
            } else {
                $table->text('embedding')->nullable();
            }

            $table->string('embedding_model')->default('BAAI/bge-small-zh-v1.5');
            $table->unsignedInteger('page_from')->nullable();
            $table->unsignedInteger('page_to')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('source_metadata')->nullable();
            $table->timestamps();

            $table->index(['insurance_policy_id', 'sort_order']);
        });

        if ($usesPgsql) {
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
