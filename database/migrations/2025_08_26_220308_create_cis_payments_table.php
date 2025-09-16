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
        Schema::create('cis_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            
            // Payment details
            $table->string('payment_reference')->nullable();
            $table->date('payment_date');
            $table->date('period_start');
            $table->date('period_end');
            
            // Financial amounts
            $table->decimal('gross_amount', 12, 2); // Total payment before deductions
            $table->decimal('materials_cost', 12, 2)->default(0); // Cost of materials (not subject to CIS)
            $table->decimal('labour_amount', 12, 2); // Labour amount (subject to CIS)
            $table->decimal('cis_rate', 5, 2); // CIS deduction rate (20% or 30%)
            $table->decimal('cis_deduction', 12, 2); // Actual CIS deduction amount
            $table->decimal('net_payment', 12, 2); // Final payment after deductions
            
            // Additional deductions
            $table->decimal('other_deductions', 12, 2)->default(0); // VAT, expenses, etc.
            $table->text('deduction_notes')->nullable();
            
            // Status and verification
            $table->enum('status', ['draft', 'verified', 'paid', 'returned'])->default('draft');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Return filing
            $table->foreignId('cis_return_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('included_in_return')->default(false);
            
            // Additional info
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'payment_date']);
            $table->index(['employee_id', 'payment_date']);
            $table->index(['project_id', 'payment_date']);
            $table->index(['status', 'payment_date']);
            $table->index('cis_return_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cis_payments');
    }
};