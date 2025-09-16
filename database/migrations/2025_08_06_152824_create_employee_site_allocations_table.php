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
        Schema::create('employee_site_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            
            // Allocation details
            $table->date('allocated_from');
            $table->date('allocated_until')->nullable();
            $table->enum('allocation_type', ['primary', 'secondary', 'temporary'])->default('primary');
            $table->text('responsibilities')->nullable(); // Specific responsibilities at this site
            $table->decimal('allocation_percentage', 5, 2)->default(100.00); // What percentage of time allocated to this site
            
            // Status
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes and constraints
            $table->unique(['employee_id', 'site_id', 'allocated_from'], 'unique_employee_site_allocation');
            $table->index(['site_id', 'status']);
            $table->index(['employee_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_site_allocations');
    }
};
