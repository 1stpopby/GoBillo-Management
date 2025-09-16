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
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Starter', 'Professional', 'Enterprise'
            $table->string('slug')->unique(); // e.g., 'starter', 'professional', 'enterprise'
            $table->text('description')->nullable();
            
            // Pricing
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_price', 10, 2)->default(0);
            $table->decimal('setup_fee', 10, 2)->default(0);
            
            // Limits
            $table->integer('max_users')->default(0); // 0 = unlimited
            $table->integer('max_sites')->default(0); // 0 = unlimited
            $table->integer('max_projects')->default(0); // 0 = unlimited
            $table->integer('max_storage_gb')->default(1); // Storage in GB
            
            // Features (boolean flags)
            $table->boolean('has_time_tracking')->default(true);
            $table->boolean('has_invoicing')->default(true);
            $table->boolean('has_reporting')->default(true);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_white_label')->default(false);
            $table->boolean('has_advanced_permissions')->default(false);
            $table->boolean('has_custom_fields')->default(false);
            $table->boolean('has_integrations')->default(false);
            $table->boolean('has_priority_support')->default(false);
            
            // Plan settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trial_available')->default(true);
            $table->integer('trial_days')->default(14);
            $table->integer('sort_order')->default(0);
            
            // Stripe integration
            $table->string('stripe_price_id_monthly')->nullable();
            $table->string('stripe_price_id_yearly')->nullable();
            $table->string('stripe_product_id')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['is_active', 'sort_order']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};