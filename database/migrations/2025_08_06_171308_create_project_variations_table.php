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
        Schema::create('project_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('variation_number')->unique(); // e.g., VAR-001
            $table->string('title');
            $table->text('description');
            $table->text('reason'); // reason for variation
            $table->enum('type', ['addition', 'omission', 'substitution', 'change_order']);
            $table->decimal('cost_impact', 10, 2)->default(0); // positive or negative
            $table->integer('time_impact_days')->default(0); // impact on schedule
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'implemented'])->default('draft');
            $table->date('requested_date');
            $table->date('required_by_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->json('affected_tasks')->nullable(); // task IDs affected by this variation
            $table->string('client_reference')->nullable();
            $table->boolean('client_approved')->default(false);
            $table->timestamp('client_approved_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['company_id', 'variation_number']);
            $table->index(['created_by', 'requested_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_variations');
    }
};
