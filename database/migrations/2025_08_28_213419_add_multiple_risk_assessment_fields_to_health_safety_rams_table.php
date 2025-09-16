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
        Schema::table('health_safety_rams', function (Blueprint $table) {
            // Add new fields for multiple risk assessments
            $table->text('task_description')->after('reference_number')->nullable();
            $table->json('risk_levels')->after('hazards')->nullable(); // Array of risk levels for each assessment
            $table->json('likelihoods')->after('risk_levels')->nullable(); // Array of likelihoods for each assessment
            $table->json('severities')->after('likelihoods')->nullable(); // Array of severities for each assessment
            $table->json('risk_control_measures')->after('severities')->nullable(); // Array of control measures per risk
            $table->text('sequence_of_work')->after('control_measures')->nullable();
            $table->text('training_required')->after('ppe_required')->nullable();
            $table->text('notes')->after('document_path')->nullable();
            
            // Rename columns to match new structure
            $table->renameColumn('valid_to', 'valid_until');
            $table->renameColumn('document_path', 'file_path');
            
            // Update status enum to match form options
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'expired'])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_safety_rams', function (Blueprint $table) {
            // Remove added fields
            $table->dropColumn([
                'task_description',
                'risk_levels',
                'likelihoods',
                'severities',
                'risk_control_measures',
                'sequence_of_work',
                'training_required',
                'notes'
            ]);
            
            // Rename columns back
            $table->renameColumn('valid_until', 'valid_to');
            $table->renameColumn('file_path', 'document_path');
            
            // Revert status enum
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'expired'])->default('draft')->change();
        });
    }
};