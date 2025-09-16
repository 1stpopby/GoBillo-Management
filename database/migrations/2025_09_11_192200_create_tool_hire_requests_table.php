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
        Schema::create('tool_hire_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            
            // Request Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('tool_category'); // 'excavation', 'power_tools', 'lifting', 'safety', etc.
            $table->string('tool_type'); // specific tool type
            $table->string('tool_name'); // specific tool/model name
            $table->integer('quantity')->default(1);
            $table->string('urgency')->default('normal'); // 'low', 'normal', 'high', 'urgent'
            
            // Hire Period
            $table->date('hire_start_date');
            $table->date('hire_end_date');
            $table->integer('hire_duration_days'); // calculated field
            $table->string('delivery_method')->default('pickup'); // 'pickup', 'delivery', 'site_delivery'
            $table->text('delivery_address')->nullable();
            $table->text('special_requirements')->nullable();
            
            // Costs
            $table->decimal('estimated_daily_rate', 10, 2)->nullable();
            $table->decimal('estimated_total_cost', 10, 2)->nullable();
            $table->decimal('actual_daily_rate', 10, 2)->nullable();
            $table->decimal('actual_total_cost', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->boolean('insurance_required')->default(false);
            $table->decimal('insurance_cost', 10, 2)->nullable();
            
            // Supplier Information
            $table->string('preferred_supplier')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->text('supplier_notes')->nullable();
            
            // Status and Workflow
            $table->enum('status', [
                'draft', 'pending_approval', 'approved', 'quoted', 'ordered', 
                'delivered', 'in_use', 'returned', 'completed', 'cancelled'
            ])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            
            // Tracking
            $table->date('actual_delivery_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->text('condition_on_delivery')->nullable();
            $table->text('condition_on_return')->nullable();
            $table->text('damage_notes')->nullable();
            $table->decimal('damage_charges', 10, 2)->nullable();
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // for photos, quotes, etc.
            
            $table->timestamps();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['requested_by']);
            $table->index(['site_id']);
            $table->index(['project_id']);
            $table->index(['tool_category']);
            $table->index(['hire_start_date']);
            $table->index(['hire_end_date']);
            $table->index(['urgency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_hire_requests');
    }
};
