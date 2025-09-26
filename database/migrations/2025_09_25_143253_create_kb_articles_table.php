<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kb_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('priority')->default(0);
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->json('meta_data')->nullable();
            $table->timestamps();
            
            $table->index('category_id');
            $table->index('slug');
            $table->index('status');
            $table->index(['status', 'published_at']);
            // Create GIN index for full-text search on PostgreSQL
            DB::statement('CREATE INDEX kb_articles_title_summary_fulltext ON kb_articles USING gin ((to_tsvector(\'english\', title) || to_tsvector(\'english\', coalesce(summary, \'\'))))');
            
            $table->foreign('category_id')->references('id')->on('kb_categories')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the full-text search index first
        DB::statement('DROP INDEX IF EXISTS kb_articles_title_summary_fulltext');
        Schema::dropIfExists('kb_articles');
    }
};
