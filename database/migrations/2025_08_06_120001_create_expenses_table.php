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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who submitted the expense
            $table->string('expense_number', 50)->unique();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'reimbursed'])->default('draft');
            $table->string('category'); // Fuel, Materials, Equipment, Meals, etc.
            $table->string('vendor')->nullable();
            $table->text('description');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('expense_date');
            $table->string('receipt_path')->nullable();
            $table->string('payment_method')->nullable(); // Cash, Credit Card, Company Card, etc.
            $table->boolean('is_billable')->default(false); // Can be billed to client
            $table->boolean('is_reimbursable')->default(true); // Should be reimbursed to employee
            $table->decimal('mileage', 8, 2)->nullable(); // For mileage expenses
            $table->decimal('mileage_rate', 5, 2)->nullable(); // Rate per mile/km
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('reimbursed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
}; 