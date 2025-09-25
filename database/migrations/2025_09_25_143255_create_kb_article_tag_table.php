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
        Schema::create('kb_article_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();
            
            $table->unique(['article_id', 'tag_id']);
            $table->index('article_id');
            $table->index('tag_id');
            
            $table->foreign('article_id')->references('id')->on('kb_articles')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('kb_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_article_tag');
    }
};
