<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kb_article_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('related_article_id');
            $table->string('relation_type')->default('related'); // related, prerequisite, sequel
            $table->timestamps();
            
            $table->unique(['article_id', 'related_article_id']);
            $table->index('article_id');
            $table->index('related_article_id');
            
            $table->foreign('article_id')->references('id')->on('kb_articles')->onDelete('cascade');
            $table->foreign('related_article_id')->references('id')->on('kb_articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_article_relations');
    }
};
