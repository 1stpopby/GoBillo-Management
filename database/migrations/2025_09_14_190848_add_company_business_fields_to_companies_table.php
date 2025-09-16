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
        Schema::table('companies', function (Blueprint $table) {
            // Business Registration Details
            $table->string('company_number')->nullable()->after('slug');
            $table->string('vat_number')->nullable()->after('company_number');
            $table->string('utr_number')->nullable()->after('vat_number'); // Unique Taxpayer Reference
            $table->string('business_type')->nullable()->after('utr_number'); // Ltd, LLP, Sole Trader, etc.
            $table->date('incorporation_date')->nullable()->after('business_type');
            
            // Contact Details (enhanced)
            $table->string('primary_contact_name')->nullable()->after('email');
            $table->string('primary_contact_title')->nullable()->after('primary_contact_name');
            $table->string('primary_contact_email')->nullable()->after('primary_contact_title');
            $table->string('primary_contact_phone')->nullable()->after('primary_contact_email');
            
            // Secondary Contact
            $table->string('secondary_contact_name')->nullable()->after('primary_contact_phone');
            $table->string('secondary_contact_title')->nullable()->after('secondary_contact_name');
            $table->string('secondary_contact_email')->nullable()->after('secondary_contact_title');
            $table->string('secondary_contact_phone')->nullable()->after('secondary_contact_email');
            
            // Address Details (enhanced)
            $table->string('registered_address')->nullable()->after('address');
            $table->string('registered_city')->nullable()->after('registered_address');
            $table->string('registered_state')->nullable()->after('registered_city');
            $table->string('registered_zip_code')->nullable()->after('registered_state');
            $table->string('registered_country', 2)->nullable()->after('registered_zip_code');
            
            // Banking Details
            $table->string('bank_name')->nullable()->after('website');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
            $table->string('bank_sort_code')->nullable()->after('bank_account_number');
            $table->string('iban')->nullable()->after('bank_sort_code');
            $table->string('swift_code')->nullable()->after('iban');
            
            // Insurance & Compliance
            $table->string('public_liability_insurer')->nullable()->after('swift_code');
            $table->string('public_liability_policy_number')->nullable()->after('public_liability_insurer');
            $table->date('public_liability_expiry')->nullable()->after('public_liability_policy_number');
            $table->decimal('public_liability_amount', 12, 2)->nullable()->after('public_liability_expiry');
            
            $table->string('employers_liability_insurer')->nullable()->after('public_liability_amount');
            $table->string('employers_liability_policy_number')->nullable()->after('employers_liability_insurer');
            $table->date('employers_liability_expiry')->nullable()->after('employers_liability_policy_number');
            $table->decimal('employers_liability_amount', 12, 2)->nullable()->after('employers_liability_expiry');
            
            // Health & Safety
            $table->string('health_safety_policy')->nullable()->after('employers_liability_amount'); // File path
            $table->date('health_safety_policy_date')->nullable()->after('health_safety_policy');
            $table->string('risk_assessment_policy')->nullable()->after('health_safety_policy_date'); // File path
            $table->date('risk_assessment_policy_date')->nullable()->after('risk_assessment_policy');
            
            // Certifications
            $table->string('construction_line_number')->nullable()->after('risk_assessment_policy_date');
            $table->date('construction_line_expiry')->nullable()->after('construction_line_number');
            $table->string('chas_number')->nullable()->after('construction_line_expiry');
            $table->date('chas_expiry')->nullable()->after('chas_number');
            $table->string('safe_contractor_number')->nullable()->after('chas_expiry');
            $table->date('safe_contractor_expiry')->nullable()->after('safe_contractor_number');
            
            // Additional Business Info
            $table->text('business_description')->nullable()->after('description');
            $table->json('services_offered')->nullable()->after('business_description'); // Array of services
            $table->string('trading_name')->nullable()->after('services_offered'); // If different from company name
            $table->string('industry_sector')->nullable()->after('trading_name'); // Construction, Electrical, etc.
            
            // Notification Preferences
            $table->json('notification_preferences')->nullable()->after('settings'); // Email, SMS preferences
            $table->string('timezone')->default('Europe/London')->after('notification_preferences');
            $table->string('currency', 3)->default('GBP')->after('timezone');
            
            // Compliance Flags
            $table->boolean('is_vat_registered')->default(false)->after('currency');
            $table->boolean('is_cis_registered')->default(false)->after('is_vat_registered');
            $table->boolean('gdpr_compliant')->default(false)->after('is_cis_registered');
            $table->date('gdpr_compliance_date')->nullable()->after('gdpr_compliant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'company_number', 'vat_number', 'utr_number', 'business_type', 'incorporation_date',
                'primary_contact_name', 'primary_contact_title', 'primary_contact_email', 'primary_contact_phone',
                'secondary_contact_name', 'secondary_contact_title', 'secondary_contact_email', 'secondary_contact_phone',
                'registered_address', 'registered_city', 'registered_state', 'registered_zip_code', 'registered_country',
                'bank_name', 'bank_account_name', 'bank_account_number', 'bank_sort_code', 'iban', 'swift_code',
                'public_liability_insurer', 'public_liability_policy_number', 'public_liability_expiry', 'public_liability_amount',
                'employers_liability_insurer', 'employers_liability_policy_number', 'employers_liability_expiry', 'employers_liability_amount',
                'health_safety_policy', 'health_safety_policy_date', 'risk_assessment_policy', 'risk_assessment_policy_date',
                'construction_line_number', 'construction_line_expiry', 'chas_number', 'chas_expiry',
                'safe_contractor_number', 'safe_contractor_expiry',
                'business_description', 'services_offered', 'trading_name', 'industry_sector',
                'notification_preferences', 'timezone', 'currency',
                'is_vat_registered', 'is_cis_registered', 'gdpr_compliant', 'gdpr_compliance_date'
            ]);
        });
    }
};