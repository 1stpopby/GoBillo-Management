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
        Schema::create('project_snaggings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->string('item_number')->unique(); // e.g., SNG-001
            $table->string('title');
            $table->text('description');
            $table->string('location'); // room, area, floor, etc.
            $table->enum('category', ['defect', 'incomplete', 'damage', 'quality', 'safety', 'compliance', 'other']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed', 'deferred'])->default('open');
            $table->date('identified_date');
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_notes')->nullable();
            $table->json('photos_before')->nullable(); // array of photo paths
            $table->json('photos_after')->nullable(); // array of photo paths
            $table->string('trade_responsible')->nullable(); // which trade/contractor
            $table->decimal('cost_to_fix', 8, 2)->nullable();
            $table->boolean('client_reported')->default(false);
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['task_id', 'status']);
            $table->index(['company_id', 'severity']);
            $table->index(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_snaggings');
    }
};
