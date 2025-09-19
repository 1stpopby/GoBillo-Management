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
        // Drop related tables first to avoid constraint issues
        Schema::dropIfExists('asset_asset_tag');
        Schema::dropIfExists('assets');

        // Recreate the assets table with correct nullable columns
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('asset_categories')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();
            $table->decimal('current_value', 12, 2)->nullable();
            $table->enum('depreciation_method', ['NONE', 'STRAIGHT_LINE'])->default('NONE');
            $table->integer('depreciation_life_months')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('model_number')->nullable();
            $table->enum('status', ['IN_STOCK', 'ASSIGNED', 'MAINTENANCE', 'RETIRED', 'LOST'])->default('IN_STOCK');
            $table->enum('condition', ['EXCELLENT', 'GOOD', 'FAIR', 'POOR'])->default('GOOD');
            $table->string('department')->nullable();
            $table->longText('notes')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('image_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'category_id']);
            $table->index(['company_id', 'location_id']);
            $table->index(['company_id', 'assignee_id']);
            $table->index(['assignee_id', 'status']);
            $table->index('purchase_date');
            $table->index('warranty_expiry');
            $table->index(['status', 'category_id']);
        });

        // Recreate the asset_asset_tag pivot table
        Schema::create('asset_asset_tag', function (Blueprint $table) {
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('asset_tag_id')->constrained('asset_tags')->onDelete('cascade');
            $table->primary(['asset_id', 'asset_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};