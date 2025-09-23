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
        Schema::create('payment_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('statement_number')->unique();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->decimal('total_budget', 10, 2)->default(0);
            $table->decimal('total_invoiced', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('outstanding_balance', 10, 2)->default(0);
            $table->decimal('remaining_budget', 10, 2)->default(0);
            $table->boolean('include_projects')->default(true);
            $table->boolean('include_invoices')->default(true);
            $table->boolean('include_payments')->default(true);
            $table->json('statement_data')->nullable(); // Store complete statement data for historical reference
            $table->date('statement_date');
            $table->string('generated_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['company_id', 'client_id']);
            $table->index(['company_id', 'statement_number']);
            $table->index('statement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_statements');
    }
};