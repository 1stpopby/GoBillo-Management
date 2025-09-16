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
        Schema::table('clients', function (Blueprint $table) {
            // Rename 'name' to 'company_name' to be more explicit
            $table->renameColumn('name', 'company_name');
            
            // Add company contact person fields
            $table->string('contact_person_name')->nullable()->after('company_name');
            $table->string('contact_person_title')->nullable()->after('contact_person_name');
            $table->string('contact_person_email')->nullable()->after('contact_person_title');
            $table->string('contact_person_phone')->nullable()->after('contact_person_email');
            
            // Add additional company fields
            $table->string('website')->nullable()->after('contact_person_phone');
            $table->string('tax_id')->nullable()->after('website');
            $table->string('business_type')->nullable()->after('tax_id'); // e.g., LLC, Corporation, Partnership
            $table->text('business_description')->nullable()->after('business_type');
            
            // Keep the original company field but rename it to 'legal_name' for clarity
            $table->renameColumn('company', 'legal_name');
            
            // Add industry field
            $table->string('industry')->nullable()->after('business_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('company_name', 'name');
            $table->renameColumn('legal_name', 'company');
            
            // Drop the added columns
            $table->dropColumn([
                'contact_person_name',
                'contact_person_title', 
                'contact_person_email',
                'contact_person_phone',
                'website',
                'tax_id',
                'business_type',
                'business_description',
                'industry'
            ]);
        });
    }
};
