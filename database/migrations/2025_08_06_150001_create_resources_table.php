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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['human', 'equipment', 'material', 'vehicle'])->default('equipment');
            $table->string('category')->nullable(); // Heavy machinery, Tools, Skilled labor, etc.
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('daily_rate', 8, 2)->nullable();
            $table->integer('max_hours_per_day')->default(8);
            $table->boolean('is_available')->default(true);
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->json('working_days')->nullable(); // [1,2,3,4,5] for Mon-Fri
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'type', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
}; 