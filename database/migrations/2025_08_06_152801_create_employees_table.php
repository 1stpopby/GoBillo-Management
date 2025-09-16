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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Link to user account if exists
            
            // Personal Information
            $table->string('employee_id')->unique(); // Company employee ID
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            
            // Address Information
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country', 2)->default('US');
            
            // Employment Information
            $table->enum('role', [
                'site_manager',
                'contract_manager', 
                'quantity_surveyor',
                'project_coordinator',
                'safety_officer',
                'quality_inspector',
                'procurement_manager',
                'construction_supervisor',
                'architect',
                'engineer',
                'foreman',
                'admin_assistant'
            ]);
            $table->string('department')->nullable();
            $table->string('job_title');
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->enum('employment_status', ['active', 'inactive', 'terminated', 'on_leave'])->default('active');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'consultant'])->default('full_time');
            
            // Compensation
            $table->decimal('salary', 10, 2)->nullable();
            $table->enum('salary_type', ['hourly', 'monthly', 'yearly'])->default('monthly');
            
            // Skills and Certifications
            $table->json('skills')->nullable(); // Array of skills
            $table->json('certifications')->nullable(); // Array of certifications with expiry dates
            $table->json('qualifications')->nullable(); // Educational qualifications
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'role']);
            $table->index(['company_id', 'employment_status']);
            $table->index(['employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
