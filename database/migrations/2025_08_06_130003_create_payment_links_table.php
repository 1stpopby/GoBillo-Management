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
        Schema::create('payment_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('uuid', 36)->unique(); // Public identifier for the payment link
            $table->string('token', 64)->unique(); // Secure token for accessing the payment link
            $table->enum('status', ['active', 'expired', 'used', 'cancelled'])->default('active');
            $table->decimal('amount', 12, 2); // Amount to be paid
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_uses')->default(1); // How many times the link can be used
            $table->integer('used_count')->default(0); // How many times it has been used
            $table->boolean('send_receipt')->default(true);
            $table->string('success_url')->nullable(); // Redirect after successful payment
            $table->string('cancel_url')->nullable(); // Redirect after cancelled payment
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_links');
    }
}; 