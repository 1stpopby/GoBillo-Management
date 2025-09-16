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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Stripe, PayPal, Square, etc.
            $table->string('type'); // credit_card, bank_transfer, digital_wallet, etc.
            $table->string('provider'); // stripe, paypal, square, etc.
            $table->text('configuration'); // JSON configuration for the payment method
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->decimal('processing_fee_percentage', 5, 2)->default(0); // e.g., 2.9%
            $table->decimal('processing_fee_fixed', 8, 2)->default(0); // e.g., $0.30
            $table->string('currency', 3)->default('USD');
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
}; 