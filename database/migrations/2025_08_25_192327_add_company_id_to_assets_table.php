<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Update existing assets to have a company_id (assuming company with ID 1 exists)
        // In a real scenario, you'd need to map assets to their correct companies
        DB::table('assets')->whereNull('company_id')->update(['company_id' => 1]);

        // Add indexes for better performance
        Schema::table('assets', function (Blueprint $table) {
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'category_id']);
            $table->index(['company_id', 'location_id']);
            $table->index(['company_id', 'assignee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['company_id', 'category_id']);
            $table->dropIndex(['company_id', 'location_id']);
            $table->dropIndex(['company_id', 'assignee_id']);
            $table->dropColumn('company_id');
        });
    }
};