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
        Schema::table('tasks', function (Blueprint $table) {
            // Add time unit fields
            $table->enum('estimated_time_unit', ['hours', 'days'])->default('hours')->after('estimated_hours');
            $table->enum('actual_time_unit', ['hours', 'days'])->default('hours')->after('actual_hours');
            
            // Rename existing columns to be more generic
            $table->renameColumn('estimated_hours', 'estimated_time');
            $table->renameColumn('actual_hours', 'actual_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('estimated_time', 'estimated_hours');
            $table->renameColumn('actual_time', 'actual_hours');
            
            // Drop the time unit columns
            $table->dropColumn(['estimated_time_unit', 'actual_time_unit']);
        });
    }
};
