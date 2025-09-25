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
        Schema::create('kb_article_bindings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->string('route_name')->nullable();
            $table->string('feature_key')->nullable();
            $table->string('model')->nullable();
            $table->string('context_type')->nullable(); // page, feature, help
            $table->timestamps();
            
            $table->index('article_id');
            $table->index('route_name');
            $table->index('feature_key');
            
            $table->foreign('article_id')->references('id')->on('kb_articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_article_bindings');
    }
};
