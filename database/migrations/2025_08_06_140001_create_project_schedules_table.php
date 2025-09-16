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
        Schema::create('project_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days'); // Calculated field
            $table->integer('progress')->default(0); // 0-100%
            $table->enum('type', ['milestone', 'task', 'phase'])->default('task');
            $table->string('color', 7)->default('#007bff'); // Hex color
            $table->integer('sort_order')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('project_schedules')->onDelete('cascade');
            $table->json('dependencies')->nullable(); // Array of schedule IDs this item depends on
            $table->boolean('is_critical_path')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'project_id']);
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_schedules');
    }
}; 