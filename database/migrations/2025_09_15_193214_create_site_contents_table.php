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
        Schema::create('site_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'hero_title', 'footer_copyright'
            $table->string('page'); // e.g., 'landing', 'get_started', 'footer'
            $table->string('section'); // e.g., 'hero', 'features', 'pricing'
            $table->string('type')->default('text'); // text, textarea, html, json, image
            $table->string('label'); // Human readable label for admin
            $table->text('value')->nullable(); // The actual content
            $table->text('default_value')->nullable(); // Fallback value
            $table->text('description')->nullable(); // Help text for admin
            $table->json('options')->nullable(); // Additional options (e.g., for select fields)
            $table->integer('sort_order')->default(0); // For ordering in admin
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_contents');
    }
};