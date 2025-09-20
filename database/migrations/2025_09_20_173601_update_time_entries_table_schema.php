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
        Schema::table('time_entries', function (Blueprint $table) {
            // Add missing columns that TimeEntry model expects
            $table->timestamp('clock_in')->nullable()->after('task_id');
            $table->timestamp('clock_out')->nullable()->after('clock_in');
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade')->after('project_id');
            $table->text('notes')->nullable()->after('distance_from_project');
            $table->string('location')->nullable()->after('notes');
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('duration')->nullable()->after('longitude')->comment('Duration in seconds');
            
            // Update status enum to include values expected by TimeEntry model
            $table->dropColumn('status');
        });
        
        // Add the new status column with correct enum values
        Schema::table('time_entries', function (Blueprint $table) {
            $table->enum('status', ['active', 'completed', 'approved', 'rejected'])->default('active')->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'clock_in',
                'clock_out', 
                'site_id',
                'notes',
                'location',
                'latitude',
                'longitude',
                'duration'
            ]);
            
            // Restore original status enum
            $table->dropColumn('status');
        });
        
        Schema::table('time_entries', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'approved', 'invoiced'])->default('draft');
        });
    }
};
