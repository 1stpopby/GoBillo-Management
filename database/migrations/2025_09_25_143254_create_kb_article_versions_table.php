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
        Schema::create('kb_article_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->integer('version')->default(1);
            $table->string('title');
            $table->longText('content_html');
            $table->longText('content_plain')->nullable();
            $table->text('change_summary')->nullable();
            $table->unsignedBigInteger('edited_by');
            $table->timestamps();
            
            $table->index('article_id');
            $table->unique(['article_id', 'version']);
            
            $table->foreign('article_id')->references('id')->on('kb_articles')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_article_versions');
    }
};
