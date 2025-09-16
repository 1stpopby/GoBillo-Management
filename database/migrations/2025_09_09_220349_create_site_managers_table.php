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
        Schema::create('site_managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['primary', 'secondary'])->default('primary');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure a manager can't be assigned to the same site twice
            $table->unique(['site_id', 'manager_id']);
            
            // Indexes for performance
            $table->index(['site_id', 'is_active']);
            $table->index(['manager_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_managers');
    }
};