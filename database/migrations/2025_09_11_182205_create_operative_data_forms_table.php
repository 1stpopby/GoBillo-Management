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
        Schema::create('operative_data_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('share_token')->unique(); // For shareable links
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Personal Information
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->string('nationality');
            $table->string('mobile_number');
            $table->string('email_address');
            $table->text('home_address');
            $table->string('postcode');
            
            // Emergency Contact
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_relationship');
            $table->string('emergency_contact_number');
            
            // Work Documentation
            $table->string('national_insurance_number');
            $table->string('utr_number')->nullable();
            $table->string('cscs_card_type')->nullable();
            $table->string('cscs_card_number')->nullable();
            $table->date('cscs_card_expiry')->nullable();
            $table->boolean('right_to_work_uk');
            $table->boolean('passport_id_provided');
            
            // Bank Details
            $table->string('bank_name');
            $table->string('account_holder_name');
            $table->string('sort_code');
            $table->string('account_number');
            
            // Trade and Qualifications
            $table->string('primary_trade');
            $table->integer('years_experience');
            $table->text('qualifications_certifications')->nullable();
            $table->text('other_cards_licenses')->nullable();
            
            // Declaration
            $table->boolean('declaration_confirmed');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index('share_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operative_data_forms');
    }
};
