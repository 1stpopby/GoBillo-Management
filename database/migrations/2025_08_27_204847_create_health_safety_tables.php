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
        // RAMS (Risk Assessment & Method Statements)
        Schema::create('health_safety_rams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('reference_number')->unique();
            $table->text('description')->nullable();
            $table->text('scope_of_work');
            $table->json('hazards')->nullable(); // Array of hazards identified
            $table->json('control_measures')->nullable(); // Array of control measures
            $table->json('ppe_required')->nullable(); // Personal protective equipment required
            $table->json('emergency_procedures')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high'])->default('medium');
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'expired'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->string('document_path')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'site_id']);
            $table->index('status');
            $table->index('risk_level');
        });

        // Toolbox Talks
        Schema::create('health_safety_toolbox_talks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('reference_number')->unique();
            $table->text('topics_covered');
            $table->text('key_points')->nullable();
            $table->foreignId('conducted_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('conducted_at');
            $table->integer('duration_minutes');
            $table->string('location');
            $table->json('attendees')->nullable(); // Array of attendee IDs and signatures
            $table->integer('attendee_count')->default(0);
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('weather_conditions', ['clear', 'cloudy', 'rain', 'snow', 'wind', 'other'])->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'site_id']);
            $table->index('conducted_at');
            $table->index('conducted_by');
        });

        // Incidents/Accidents
        Schema::create('health_safety_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('incident_number')->unique();
            $table->enum('type', ['accident', 'near_miss', 'dangerous_occurrence', 'environmental', 'property_damage']);
            $table->enum('severity', ['minor', 'moderate', 'serious', 'major', 'fatal'])->default('minor');
            $table->dateTime('occurred_at');
            $table->string('location');
            $table->text('description');
            $table->json('involved_persons')->nullable(); // Array of person details
            $table->json('witnesses')->nullable(); // Array of witness details
            $table->text('immediate_actions')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->boolean('first_aid_given')->default(false);
            $table->boolean('medical_treatment_required')->default(false);
            $table->boolean('reported_to_hse')->default(false);
            $table->boolean('reportable_riddor')->default(false); // UK specific
            $table->integer('days_lost')->default(0);
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('investigated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('investigation_date')->nullable();
            $table->enum('status', ['reported', 'under_investigation', 'closed', 'follow_up_required'])->default('reported');
            $table->json('attachments')->nullable(); // Photos, documents
            $table->timestamps();
            
            $table->index(['company_id', 'site_id']);
            $table->index('type');
            $table->index('severity');
            $table->index('occurred_at');
            $table->index('status');
        });

        // Site Inductions
        Schema::create('health_safety_inductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('inductee_name');
            $table->string('inductee_company')->nullable();
            $table->string('inductee_role')->nullable();
            $table->string('inductee_phone')->nullable();
            $table->string('inductee_email')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->dateTime('inducted_at');
            $table->foreignId('inducted_by')->constrained('users')->onDelete('cascade');
            $table->json('topics_covered')->nullable(); // Array of topics
            $table->json('documents_provided')->nullable(); // Array of document names
            $table->boolean('site_rules_acknowledged')->default(false);
            $table->boolean('emergency_procedures_understood')->default(false);
            $table->boolean('ppe_requirements_understood')->default(false);
            $table->boolean('hazards_communicated')->default(false);
            $table->date('valid_until');
            $table->string('certificate_number')->unique()->nullable();
            $table->string('signature_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->timestamps();
            
            $table->index(['company_id', 'site_id']);
            $table->index('employee_id');
            $table->index('inducted_at');
            $table->index('valid_until');
            $table->index('status');
        });

        // Custom Forms Templates
        Schema::create('health_safety_form_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('category', ['inspection', 'permit', 'checklist', 'assessment', 'report', 'other'])->default('other');
            $table->json('fields')->nullable(); // JSON schema for form fields
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_signature')->default(true);
            $table->boolean('requires_photo')->default(false);
            $table->integer('version')->default(1);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['company_id', 'category']);
            $table->index('is_active');
        });

        // Custom Form Submissions
        Schema::create('health_safety_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained('health_safety_form_templates')->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('submission_number')->unique();
            $table->json('form_data'); // Submitted form data
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('submitted_at');
            $table->string('signature_path')->nullable();
            $table->json('photos')->nullable(); // Array of photo paths
            $table->json('attachments')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected'])->default('submitted');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_comments')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'template_id']);
            $table->index(['site_id', 'project_id']);
            $table->index('submitted_at');
            $table->index('status');
        });

        // PPE Register
        Schema::create('health_safety_ppe_register', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_name');
            $table->string('ppe_type'); // Hard hat, safety boots, hi-vis, etc.
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('size')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
            $table->enum('condition', ['new', 'good', 'fair', 'poor', 'replace'])->default('new');
            $table->date('last_inspection_date')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->boolean('returned')->default(false);
            $table->date('returned_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'employee_id']);
            $table->index('issued_date');
            $table->index('expiry_date');
        });

        // Safety Observations
        Schema::create('health_safety_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['positive', 'negative', 'improvement'])->default('improvement');
            $table->string('category'); // Behavior, condition, process, etc.
            $table->text('observation');
            $table->text('action_taken')->nullable();
            $table->text('recommendation')->nullable();
            $table->foreignId('observed_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('observed_at');
            $table->string('location');
            $table->json('photos')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('target_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'site_id']);
            $table->index('type');
            $table->index('observed_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_safety_observations');
        Schema::dropIfExists('health_safety_ppe_register');
        Schema::dropIfExists('health_safety_form_submissions');
        Schema::dropIfExists('health_safety_form_templates');
        Schema::dropIfExists('health_safety_inductions');
        Schema::dropIfExists('health_safety_incidents');
        Schema::dropIfExists('health_safety_toolbox_talks');
        Schema::dropIfExists('health_safety_rams');
    }
};