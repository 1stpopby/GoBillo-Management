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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null');
            $table->string('payment_number', 50)->unique();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->decimal('amount', 12, 2);
            $table->decimal('processing_fee', 8, 2)->default(0);
            $table->decimal('net_amount', 12, 2); // amount - processing_fee
            $table->string('currency', 3)->default('USD');
            $table->string('payment_type'); // full_payment, partial_payment, deposit
            $table->string('provider_transaction_id')->nullable(); // Stripe charge ID, PayPal transaction ID, etc.
            $table->text('provider_response')->nullable(); // JSON response from payment provider
            $table->string('payment_gateway'); // stripe, paypal, square, manual, etc.
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->string('refund_reason')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index('provider_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}; 