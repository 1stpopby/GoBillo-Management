<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('asset_categories')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->unsignedBigInteger('vendor_id');
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 12, 2);
            $table->enum('depreciation_method', ['NONE', 'STRAIGHT_LINE'])->default('NONE');
            $table->integer('depreciation_life_months')->nullable();
            $table->enum('status', ['IN_STOCK', 'ASSIGNED', 'MAINTENANCE', 'RETIRED', 'LOST'])->default('IN_STOCK');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('department')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->date('warranty_expiry')->nullable();
            $table->longText('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['status', 'category_id']);
            $table->index(['assignee_id', 'status']);
            $table->index('purchase_date');
            $table->index('warranty_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};