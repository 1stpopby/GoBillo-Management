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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Page title
            $table->string('slug')->unique(); // URL slug
            $table->longText('content'); // Page content (HTML)
            $table->text('excerpt')->nullable(); // Short description
            $table->string('meta_title')->nullable(); // SEO title
            $table->text('meta_description')->nullable(); // SEO description
            $table->string('meta_keywords')->nullable(); // SEO keywords
            $table->string('template')->default('default'); // Template to use
            $table->boolean('is_published')->default(true);
            $table->boolean('show_in_footer')->default(false); // Auto-add to footer
            $table->string('footer_section')->nullable(); // Which footer section
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};