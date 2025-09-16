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
        Schema::table('project_expenses', function (Blueprint $table) {
            // Add VAT fields
            $table->decimal('net_amount', 10, 2)->after('amount')->default(0);
            $table->decimal('vat_amount', 10, 2)->after('net_amount')->default(0);
            $table->decimal('vat_rate', 5, 2)->after('vat_amount')->default(20.00); // Default 20% VAT
            
            // Remove title and description columns
            $table->dropColumn(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            // Remove VAT fields
            $table->dropColumn(['net_amount', 'vat_amount', 'vat_rate']);
            
            // Add back title and description
            $table->string('title')->after('created_by');
            $table->text('description')->nullable()->after('title');
        });
    }
};