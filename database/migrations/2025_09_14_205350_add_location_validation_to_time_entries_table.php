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
            // Operative's actual location when clocking in/out
            $table->decimal('operative_latitude', 10, 8)->nullable()->after('longitude');
            $table->decimal('operative_longitude', 11, 8)->nullable()->after('operative_latitude');
            $table->string('operative_location_address')->nullable()->after('operative_longitude');
            
            // Project location for validation
            $table->decimal('project_latitude', 10, 8)->nullable()->after('operative_location_address');
            $table->decimal('project_longitude', 11, 8)->nullable()->after('project_latitude');
            
            // Distance validation
            $table->decimal('distance_from_project', 8, 2)->nullable()->after('project_longitude')->comment('Distance in meters');
            $table->boolean('location_validated')->default(false)->after('distance_from_project');
            $table->text('location_validation_error')->nullable()->after('location_validated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropColumn([
                'operative_latitude', 
                'operative_longitude', 
                'operative_location_address',
                'project_latitude', 
                'project_longitude', 
                'distance_from_project',
                'location_validated',
                'location_validation_error'
            ]);
        });
    }
};