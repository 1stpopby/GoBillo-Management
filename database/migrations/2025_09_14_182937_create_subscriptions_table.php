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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_plan_id')->constrained()->cascadeOnDelete();
            
            // Subscription details
            $table->enum('status', ['active', 'inactive', 'cancelled', 'past_due', 'trial', 'suspended'])->default('trial');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            
            // Dates
            $table->datetime('starts_at');
            $table->datetime('ends_at')->nullable();
            $table->datetime('trial_ends_at')->nullable();
            $table->datetime('cancelled_at')->nullable();
            
            // Pricing (stored for historical purposes)
            $table->decimal('amount', 10, 2);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->string('currency', 3)->default('GBP');
            
            // Stripe integration
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->json('stripe_data')->nullable(); // Store additional Stripe data
            
            // Usage tracking
            $table->integer('current_users')->default(0);
            $table->integer('current_sites')->default(0);
            $table->integer('current_projects')->default(0);
            $table->decimal('current_storage_gb', 8, 2)->default(0);
            
            // Billing
            $table->datetime('next_billing_date')->nullable();
            $table->datetime('last_payment_date')->nullable();
            $table->decimal('last_payment_amount', 10, 2)->nullable();
            
            // Notes and metadata
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index('status');
            $table->index('next_billing_date');
            $table->index('stripe_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};