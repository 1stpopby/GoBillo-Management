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
        Schema::create('hire_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            
            // Request Details
            $table->string('title');
            $table->text('description');
            $table->string('position_type'); // 'operative', 'supervisor', 'manager', etc.
            $table->string('employment_type')->default('full_time'); // 'full_time', 'part_time', 'contract'
            $table->integer('quantity')->default(1); // Number of people needed
            $table->string('urgency')->default('normal'); // 'low', 'normal', 'high', 'urgent'
            
            // Requirements
            $table->text('required_skills')->nullable();
            $table->text('required_qualifications')->nullable();
            $table->text('required_certifications')->nullable();
            $table->integer('min_experience_years')->nullable();
            
            // Compensation
            $table->decimal('offered_rate', 10, 2)->nullable();
            $table->string('rate_type')->default('daily'); // 'hourly', 'daily', 'weekly', 'monthly'
            $table->text('benefits')->nullable();
            
            // Timeline
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable(); // For contract positions
            $table->date('deadline')->nullable(); // Deadline to fill position
            
            // Status and Workflow
            $table->enum('status', [
                'draft', 'pending_approval', 'approved', 'in_progress', 
                'filled', 'cancelled', 'expired'
            ])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // HR/Recruiter
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            
            // Tracking
            $table->integer('applications_count')->default(0);
            $table->integer('interviews_count')->default(0);
            $table->integer('hired_count')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['requested_by']);
            $table->index(['site_id']);
            $table->index(['project_id']);
            $table->index(['urgency']);
            $table->index(['start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hire_requests');
    }
};
