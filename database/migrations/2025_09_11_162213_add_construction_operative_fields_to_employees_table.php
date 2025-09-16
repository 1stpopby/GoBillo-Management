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
        Schema::table('employees', function (Blueprint $table) {
            // Personal Information - additional fields
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->string('postcode')->nullable()->after('zip_code');
            
            // Work Documentation
            $table->string('national_insurance_number')->nullable()->after('cis_rate');
            $table->string('utr_number')->nullable()->after('national_insurance_number');
            $table->string('cscs_card_type')->nullable()->after('utr_number');
            $table->string('cscs_card_number')->nullable()->after('cscs_card_type');
            $table->date('cscs_card_expiry')->nullable()->after('cscs_card_number');
            $table->boolean('right_to_work_uk')->default(false)->after('cscs_card_expiry');
            $table->boolean('passport_id_provided')->default(false)->after('right_to_work_uk');
            
            // Bank Details
            $table->string('bank_name')->nullable()->after('passport_id_provided');
            $table->string('account_holder_name')->nullable()->after('bank_name');
            $table->string('sort_code')->nullable()->after('account_holder_name');
            $table->string('account_number')->nullable()->after('sort_code');
            
            // Trade and Qualifications - enhanced
            $table->string('primary_trade')->nullable()->after('account_number');
            $table->integer('years_experience')->nullable()->after('primary_trade');
            $table->json('other_cards_licenses')->nullable()->after('qualifications'); // CPCS, NPORS, etc.
            
            // Add indexes for commonly searched fields
            $table->index('national_insurance_number');
            $table->index('cscs_card_number');
            $table->index('primary_trade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['national_insurance_number']);
            $table->dropIndex(['cscs_card_number']);
            $table->dropIndex(['primary_trade']);
            
            $table->dropColumn([
                'nationality',
                'postcode',
                'national_insurance_number',
                'utr_number',
                'cscs_card_type',
                'cscs_card_number',
                'cscs_card_expiry',
                'right_to_work_uk',
                'passport_id_provided',
                'bank_name',
                'account_holder_name',
                'sort_code',
                'account_number',
                'primary_trade',
                'years_experience',
                'other_cards_licenses'
            ]);
        });
    }
};