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
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->nullable(); // Calculated or manual
            $table->text('description')->nullable();
            $table->boolean('is_billable')->default(true);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable(); // duration * hourly_rate
            $table->enum('status', ['draft', 'submitted', 'approved', 'invoiced'])->default('draft');
            $table->boolean('is_running')->default(false); // For active time tracking
            $table->timestamps();

            $table->index(['company_id', 'user_id', 'date']);
            $table->index(['project_id', 'date']);
            $table->index(['user_id', 'is_running']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
}; 