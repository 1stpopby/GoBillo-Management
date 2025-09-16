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
            $table->string('task_name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('actual_start')->nullable();
            $table->date('actual_end')->nullable();
            $table->integer('duration_days')->default(1);
            $table->decimal('progress', 5, 2)->default(0); // 0-100
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'delayed', 'on_hold', 'cancelled'])->default('not_started');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_task_id')->nullable()->constrained('project_schedules')->nullOnDelete();
            $table->json('dependencies')->nullable(); // Array of task IDs this task depends on
            $table->boolean('is_milestone')->default(false);
            $table->string('color', 7)->nullable(); // Hex color for Gantt chart
            $table->integer('order_index')->default(0); // For sorting tasks
            $table->json('resources')->nullable(); // Array of resource IDs/names
            $table->decimal('estimated_hours', 10, 2)->nullable();
            $table->decimal('actual_hours', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'project_id']);
            $table->index(['start_date', 'end_date']);
            $table->index('status');
            $table->index('assigned_to');
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