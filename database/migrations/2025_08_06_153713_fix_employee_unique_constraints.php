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
            // Drop existing unique constraints
            $table->dropUnique(['employee_id']);
            $table->dropUnique(['email']);
            
            // Add composite unique constraints per company
            $table->unique(['company_id', 'employee_id'], 'employees_company_employee_id_unique');
            $table->unique(['company_id', 'email'], 'employees_company_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop composite unique constraints
            $table->dropUnique('employees_company_employee_id_unique');
            $table->dropUnique('employees_company_email_unique');
            
            // Restore original unique constraints
            $table->unique('employee_id');
            $table->unique('email');
        });
    }
};
