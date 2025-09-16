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
        Schema::create('cis_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Return period
            $table->integer('tax_year'); // e.g., 2024
            $table->integer('tax_month'); // 1-12
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_date'); // 19th of following month
            
            // Return summary
            $table->integer('total_subcontractors')->default(0);
            $table->decimal('total_payments', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('total_materials', 12, 2)->default(0);
            
            // Submission details
            $table->enum('status', ['draft', 'submitted', 'accepted', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->string('hmrc_reference')->nullable(); // HMRC submission reference
            $table->json('submission_response')->nullable(); // HMRC API response
            
            // Filing details
            $table->foreignId('prepared_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            
            // Penalties and corrections
            $table->boolean('is_late')->default(false);
            $table->decimal('penalty_amount', 10, 2)->nullable();
            $table->boolean('is_correction')->default(false);
            $table->foreignId('corrects_return_id')->nullable()->constrained('cis_returns')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes and constraints
            $table->unique(['company_id', 'tax_year', 'tax_month'], 'unique_company_return_period');
            $table->index(['company_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cis_returns');
    }
};